@include('common.navigations.header')

@php
    $canManageSchedulerDesktop = in_array(($role ?? session('role')), ['super', 'admin'], true);
    $selectedDisplayId = (int) request('display_id', 0);
    $schedulerText = [
        'pleaseSelect' => __('Please select'),
        'selectFacilityFirst' => __('Select facility first'),
        'selectWorkgroupFirst' => __('Select workgroup first'),
        'selectWorkstationFirst' => __('Select workstation first'),
        'noFacilitiesFound' => __('No facilities found'),
        'noWorkgroupsFound' => __('No workgroups found'),
        'noWorkstationsFound' => __('No workstations found'),
        'noDisplaysFound' => __('No displays found'),
        'allDisplaysInScope' => __('All displays in scope'),
        'display' => __('Display'),
        'displays' => __('Displays'),
        'workstation' => __('Workstation'),
        'workgroup' => __('Workgroup'),
        'facility' => __('Facility'),
        'selected' => __('selected'),
        'scheduleTask' => __('Schedule Task'),
        'deleteTask' => __('Delete Task'),
        'failedToDeleteTask' => __('Failed to delete task.'),
        'taskDeletedSuccessfully' => __('Task deleted successfully.'),
        'noActions' => __('No actions'),
        'searchSchedulerTasks' => __('Search scheduler tasks...'),
        'task' => __('Task'),
        'schedule' => __('Schedule'),
        'created' => __('Created'),
        'dueDate' => __('Due Date'),
        'status' => __('Status'),
        'enabled' => __('Enabled'),
        'actions' => __('Actions'),
    ];
@endphp

@push('head')
<style>
    #tasks-grid .gridjs-th {
        vertical-align: middle !important;
        height: 64px !important;
        padding: 0 1.75rem !important;
    }

    #tasks-grid .gridjs-th-content {
        display: flex !important;
        align-items: center !important;
        width: 100%;
        min-height: 64px;
        line-height: 1 !important;
        justify-content: flex-start !important;
        white-space: nowrap;
        padding: 0 !important;
        margin: 0 !important;
    }

    #tasks-grid .gridjs-th:last-child .gridjs-th-content {
        justify-content: flex-end !important;
    }

    #tasks-grid .gridjs-th > div {
        display: flex !important;
        align-items: center !important;
        min-height: 64px;
        height: 64px !important;
    }

    #tasks-grid .gridjs-td {
        vertical-align: middle !important;
        padding: 1.1rem 1.75rem !important;
        overflow: visible !important;
    }

    #tasks-grid .scheduler-cell {
        display: flex;
        min-height: 0;
        align-items: center;
        line-height: 1.35;
    }

    #tasks-grid .scheduler-cell-stack {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        gap: 0.25rem;
        line-height: 1.35;
    }

    #tasks-grid .scheduler-cell-action {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        line-height: 1;
        position: relative;
        z-index: 5;
    }

    #tasks-grid .scheduler-cell-action > .relative {
        display: inline-flex;
        align-items: center;
    }

    .scheduler-grid-shell,
    .scheduler-grid-shell > div,
    .scheduler-grid-shell .gridjs-container,
    .scheduler-grid-shell .gridjs-wrapper,
    .scheduler-grid-shell .gridjs-footer {
        overflow: visible !important;
    }
</style>
@endpush

<div class="space-y-6" x-data="schedulerPage()">
    <section class="rounded-[2rem] border border-slate-200 bg-white px-7 py-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl border border-slate-200 bg-sky-50 text-sky-600 shadow-sm">
                    <i data-lucide="calendar-days" class="h-6 w-6"></i>
                </div>
                <div class="space-y-2">
                    <p class="text-[11px] font-black uppercase tracking-[0.28em] text-slate-400">{{ __('Admin Workspace') }}</p>
                    <h1 class="text-4xl font-extrabold tracking-tight text-slate-900">{{ __('Task Scheduler') }}</h1>
                    <p class="max-w-3xl text-sm text-slate-500">
                        {{ __('Manage automated and manual verification sequences across calibration and QA tasks.') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ url('reports/all-tasks') }}" target="_blank"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                    <i data-lucide="download" class="h-4 w-4"></i>
                    {{ __('Export Report') }}
                </a>
            </div>
        </div>
    </section>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
        <div class="mb-6 border-b border-slate-200">
            <div class="flex items-center gap-2">
                <button type="button"
                    @click="activeTab = 'tasks'"
                    class="rounded-t-2xl px-4 py-3 text-sm font-semibold transition"
                    :class="activeTab === 'tasks' ? 'border-b-2 border-sky-500 text-sky-600' : 'text-slate-500 hover:text-slate-700'">
                    {{ __('Schedule Tasks') }}
                </button>
                <button type="button"
                    @click="activeTab = 'calendar'; initCalendarIfNeeded()"
                    class="rounded-t-2xl px-4 py-3 text-sm font-semibold transition"
                    :class="activeTab === 'calendar' ? 'border-b-2 border-sky-500 text-sky-600' : 'text-slate-500 hover:text-slate-700'">
                    {{ __('Calendar') }}
                </button>
            </div>
        </div>

        <div x-show="activeTab === 'tasks'" x-cloak>
            @if($canManageSchedulerDesktop)
            <div class="mb-6 rounded-[1.75rem] border border-slate-200 bg-slate-50/70 p-5 shadow-[0_12px_34px_rgba(15,23,42,0.05)]"
                x-data="{ openScheduleDisplays: false }">
                <div class="mb-4">
                    <p class="text-[11px] font-black uppercase tracking-[0.28em] text-slate-400">{{ __('Create Schedule') }}</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ __('Schedule tasks by hierarchy') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ __('Pick a facility, workgroup, workstation, and optional displays, then continue to the schedule editor.') }}</p>
                </div>

                <form id="scheduler-create-form" onsubmit="event.preventDefault(); window.create_task(this)">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
                        <label class="block">
                            <span class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Facility') }}</span>
                            <select name="facility2" id="schedule_facility_field"
                                onchange="fetch_schedule_workgroups(this)"
                                class="hidden">
                                <option value="">{{ __('Please select') }}</option>
                                @if(($role ?? session('role')) !== 'super')
                                    @foreach($facilities as $fc)
                                        <option value="{{ $fc['id'] }}">{{ $fc['name'] }}</option>
                                    @endforeach
                                @else
                                    @foreach($facilities as $fc)
                                        <option value="{{ $fc->id }}">{{ $fc->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="relative">
                                <button type="button" id="schedule-facility-trigger"
                                    class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-900 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                    <span id="schedule-facility-label" class="truncate">{{ __('Please select') }}</span>
                                    <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                                </button>
                                <div id="schedule-facility-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                                    <input id="schedule-facility-search" type="text" placeholder="{{ __('Search facilities...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                    <p id="schedule-facility-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                                    <div id="schedule-facility-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                                </div>
                            </div>
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Workgroup') }}</span>
                            <select name="workgroup2" id="schedule_workgroups_field"
                                onchange="fetch_schedule_workstations(this)"
                                class="hidden">
                                <option value="">{{ __('Select facility first') }}</option>
                            </select>
                            <div class="relative">
                                <button type="button" id="schedule-workgroup-trigger"
                                    class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-900 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                                    <span id="schedule-workgroup-label" class="truncate">{{ __('Select facility first') }}</span>
                                    <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                                </button>
                                <div id="schedule-workgroup-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                                    <input id="schedule-workgroup-search" type="text" placeholder="{{ __('Search workgroups...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                    <p id="schedule-workgroup-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                                    <div id="schedule-workgroup-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                                </div>
                            </div>
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Workstation') }}</span>
                            <select name="workstation2" id="schedule_workstations_field"
                                onchange="fetch_schedule_displays(this)"
                                class="hidden">
                                <option value="">{{ __('Select workgroup first') }}</option>
                            </select>
                            <div class="relative">
                                <button type="button" id="schedule-workstation-trigger"
                                    class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-900 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                                    <span id="schedule-workstation-label" class="truncate">{{ __('Select workgroup first') }}</span>
                                    <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                                </button>
                                <div id="schedule-workstation-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                                    <input id="schedule-workstation-search" type="text" placeholder="{{ __('Search workstations...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                    <p id="schedule-workstation-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                                    <div id="schedule-workstation-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                                </div>
                            </div>
                        </label>

                        <div class="relative">
                            <span class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Displays') }}</span>
                            <button type="button"
                                @click="openScheduleDisplays = !openScheduleDisplays"
                                @click.away="openScheduleDisplays = false"
                                id="schedule_displays_dropdown"
                                class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-900 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                <span id="schedule_displays_label" class="truncate">{{ __('Select workstation first') }}</span>
                                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-200" :class="openScheduleDisplays ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="openScheduleDisplays" x-cloak
                                id="schedule_displays_field"
                                class="absolute left-0 right-0 z-40 mt-2 max-h-72 overflow-auto rounded-[1.25rem] border border-slate-200 bg-white p-2 shadow-[0_20px_45px_rgba(15,23,42,0.14)]"
                                style="display:none;">
                                <div class="border-b border-slate-100 p-1 pb-3">
                                    <input id="schedule-displays-search" type="text" placeholder="{{ __('Search displays...') }}" class="h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                </div>
                                <div id="schedule-displays-options" class="pt-2">
                                    <div class="px-3 py-2 text-sm text-slate-500">{{ __('Select workstation first') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-full bg-sky-500 px-6 text-sm font-semibold text-white shadow-[0_16px_30px_rgba(14,165,233,0.24)] transition hover:bg-sky-400">
                                <i data-lucide="plus" class="h-4 w-4"></i>
                                {{ __('Add Schedule') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif

            <div class="mb-4 flex items-center justify-between gap-4">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-[0.28em] text-slate-400">{{ __('Scheduled Tasks') }}</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ __('All calibration and QA schedules') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ __('Nearest due schedules are shown first so upcoming work is easier to prioritize.') }}</p>
                </div>
            </div>

            <div class="scheduler-grid-shell overflow-visible rounded-[1.75rem] border border-slate-200 bg-white shadow-[0_16px_44px_rgba(15,23,42,0.06)]">
                <x-data-table id="tasks-grid" class="overflow-visible" />
            </div>
        </div>

        <div x-show="activeTab === 'calendar'" x-cloak class="space-y-4">
            <div>
                <p class="text-[11px] font-black uppercase tracking-[0.28em] text-slate-400">{{ __('Calendar') }}</p>
                <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ __('Task calendar overview') }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ __('Browse scheduled calibration and QA activity in a monthly, weekly, or daily view.') }}</p>
            </div>

            <div class="scheduler-calendar-shell overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-[0_16px_44px_rgba(15,23,42,0.06)]">
                <div id="scheduler-calendar"></div>
            </div>
        </div>
    </section>
</div>

<div x-data="{ open: false, event: null }"
    @scheduler-event.window="open = true; event = $event.detail"
    class="relative z-[9999]">
    <div x-show="open" x-cloak class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    <div x-show="open" x-cloak class="fixed inset-0 z-10 flex items-center justify-center p-4">
        <div @click.away="open = false"
            class="w-full max-w-xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)]">
            <div class="border-b border-slate-200 px-6 py-5">
                <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">{{ __('Task Detail') }}</p>
                <h3 class="mt-2 text-2xl font-bold tracking-tight text-slate-900" x-text="event?.title || '-'"></h3>
                <p class="mt-1 text-sm text-slate-500" x-text="event?.subtitle || '-'"></p>
            </div>
            <div class="grid grid-cols-1 gap-4 px-6 py-5 md:grid-cols-2">
                <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4">
                    <p class="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('When') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900" x-text="event?.dateLabel || '-'"></p>
                </div>
                <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4">
                    <p class="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Task Type') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900" x-text="event?.badgeLabel || '-'"></p>
                </div>
                <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4 md:col-span-2">
                    <p class="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Location') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900" x-text="event?.locationLabel || '-'"></p>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                <button type="button" @click="open = false"
                    class="inline-flex h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function schedulerPage() {
        return {
            activeTab: 'tasks',
            calendarReady: false,
            calendarInstance: null,
            async initCalendarIfNeeded() {
                if (this.calendarReady) return;
                this.calendarReady = true;
                const el = document.getElementById('scheduler-calendar');
                if (!el) return;

                this.calendarInstance = await window.Perfectlum.createSchedulerCalendar({
                    element: el,
                    eventsUrl: @json(url('/calendar/events')),
                    onEventClick(event) {
                        window.dispatchEvent(new CustomEvent('scheduler-event', { detail: event }));
                    }
                });
            }
        };
    }

    (function () {
        const text = @json($schedulerText);
        const scheduleHierarchyState = {
            activeDropdown: null,
            facilitySearch: '',
            workgroupSearch: '',
            workstationSearch: '',
        };
        const canManageTasks = @json($canManageSchedulerDesktop);

        function updateScheduleDisplaysLabel() {
            const field = document.getElementById('schedule-displays-options');
            const label = document.getElementById('schedule_displays_label');
            if (!field || !label) return;
            const selectAll = field.querySelector('input[data-select-all="1"]');
            const regularItems = Array.from(field.querySelectorAll('input[name="displays[]"]:not([data-select-all="1"])'));

            if (regularItems.length && selectAll) {
                const allChecked = regularItems.every((item) => item.checked);
                selectAll.checked = allChecked;
            }

            if (!regularItems.length) {
                label.textContent = text.noDisplaysFound;
                return;
            }

            const selectedRegular = regularItems.filter((item) => item.checked);
            if (!selectedRegular.length || selectedRegular.length === regularItems.length) {
                label.textContent = text.allDisplaysInScope;
                return;
            }

            label.textContent = selectedRegular.length === 1
                ? selectedRegular[0].dataset.label
                : `${selectedRegular.length} ${text.displays} ${text.selected}`;
        }

        function resetScheduleDisplays(message) {
            const field = document.getElementById('schedule-displays-options');
            const label = document.getElementById('schedule_displays_label');
            if (field) {
                field.innerHTML = `<div class="px-3 py-2 text-sm text-slate-500">${message}</div>`;
            }
            if (label) {
                label.textContent = message;
            }
        }

        function parseScheduleSelectOptions(selectId) {
            const select = document.getElementById(selectId);
            if (!select) return [];
            return Array.from(select.options || []).map((option) => ({
                value: option.value,
                label: option.textContent.trim(),
            }));
        }

        function renderScheduleNativeOptions(selectId, optionsId, hintId, query, onPick, emptyText) {
            const options = parseScheduleSelectOptions(selectId).filter((item, index) => index > 0);
            const filtered = options.filter((item) => item.label.toLowerCase().includes((query || '').trim().toLowerCase()));
            const box = document.getElementById(optionsId);
            const hint = document.getElementById(hintId);
            if (hint) hint.textContent = filtered.length ? `${filtered.length} option${filtered.length === 1 ? '' : 's'}` : emptyText;
            if (!box) return;
            box.innerHTML = filtered.length
                ? filtered.map((item) => `<button type="button" data-value="${Perfectlum.escapeHtml(item.value)}" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 hover:bg-sky-50 hover:text-sky-700">${Perfectlum.escapeHtml(item.label)}</button>`).join('')
                : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">${emptyText}</div>`;
            box.querySelectorAll('button[data-value]').forEach((button) => {
                button.addEventListener('click', () => onPick(button.dataset.value));
            });
        }

        function refreshScheduleFacilityOptions() {
            renderScheduleNativeOptions(
                'schedule_facility_field',
                'schedule-facility-options',
                'schedule-facility-hint',
                scheduleHierarchyState.facilitySearch,
                (value) => {
                    const select = document.getElementById('schedule_facility_field');
                    select.value = value;
                    document.getElementById('schedule-facility-label').textContent = select.options[select.selectedIndex]?.textContent || text.pleaseSelect;
                    fetch_schedule_workgroups(select);
                    closeScheduleDropdowns();
                },
                text.noFacilitiesFound
            );
        }

        function refreshScheduleWorkgroupOptions() {
            renderScheduleNativeOptions(
                'schedule_workgroups_field',
                'schedule-workgroup-options',
                'schedule-workgroup-hint',
                scheduleHierarchyState.workgroupSearch,
                (value) => {
                    const select = document.getElementById('schedule_workgroups_field');
                    select.value = value;
                    document.getElementById('schedule-workgroup-label').textContent = select.options[select.selectedIndex]?.textContent || text.workgroup;
                    fetch_schedule_workstations(select);
                    closeScheduleDropdowns();
                },
                text.noWorkgroupsFound
            );
        }

        function refreshScheduleWorkstationOptions() {
            renderScheduleNativeOptions(
                'schedule_workstations_field',
                'schedule-workstation-options',
                'schedule-workstation-hint',
                scheduleHierarchyState.workstationSearch,
                (value) => {
                    const select = document.getElementById('schedule_workstations_field');
                    select.value = value;
                    document.getElementById('schedule-workstation-label').textContent = select.options[select.selectedIndex]?.textContent || text.workstation;
                    fetch_schedule_displays(select);
                    closeScheduleDropdowns();
                },
                text.noWorkstationsFound
            );
        }

        window.fetch_schedule_workgroups = function (th) {
            const formData = new FormData();
            formData.append('_token', @json(csrf_token()));
            formData.append('id', th.value || '');

            fetch(@json(url('fetch-groups2')), {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
                .then((res) => res.json())
                .then((data) => {
                    if (!data.success) return;
                    const workgroups = document.getElementById('schedule_workgroups_field');
                    const workstations = document.getElementById('schedule_workstations_field');
                    if (workgroups) workgroups.innerHTML = data.content;
                    if (workstations) workstations.innerHTML = `<option value="">${text.selectWorkgroupFirst}</option>`;
                    document.getElementById('schedule-workgroup-label').textContent = text.workgroup;
                    document.getElementById('schedule-workstation-label').textContent = text.selectWorkgroupFirst;
                    scheduleHierarchyState.workgroupSearch = '';
                    scheduleHierarchyState.workstationSearch = '';
                    const workgroupSearch = document.getElementById('schedule-workgroup-search');
                    const workstationSearch = document.getElementById('schedule-workstation-search');
                    if (workgroupSearch) workgroupSearch.value = '';
                    if (workstationSearch) workstationSearch.value = '';
                    resetScheduleDisplays(text.selectWorkstationFirst);
                    refreshScheduleWorkgroupOptions();
                });
        };

        window.fetch_schedule_workstations = function (th) {
            const formData = new FormData();
            formData.append('_token', @json(csrf_token()));
            formData.append('id', th.value || '');

            fetch(@json(url('fetch-workstations2')), {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
                .then((res) => res.json())
                .then((data) => {
                    if (!data.success) return;
                    const workstations = document.getElementById('schedule_workstations_field');
                    if (workstations) workstations.innerHTML = data.content;
                    document.getElementById('schedule-workstation-label').textContent = text.workstation;
                    scheduleHierarchyState.workstationSearch = '';
                    const workstationSearch = document.getElementById('schedule-workstation-search');
                    if (workstationSearch) workstationSearch.value = '';
                    resetScheduleDisplays(text.selectWorkstationFirst);
                    refreshScheduleWorkstationOptions();
                });
        };

        window.fetch_schedule_displays = function (th) {
            const formData = new FormData();
            formData.append('_token', @json(csrf_token()));
            formData.append('id', th.value || '');

            fetch(@json(url('fetch-displays-checklist2')), {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
                .then((res) => res.json())
                .then((data) => {
                    if (!data.success) return;

                    const field = document.getElementById('schedule-displays-options');
                    if (!field) return;

                    let content = data.content || '';
                    if (!content.trim()) {
                        resetScheduleDisplays(text.noDisplaysFound);
                        return;
                    }

                    content = content
                        .replace(/name='displays2\\[\\]'/g, "name='displays[]'")
                        .replace(/name=\"displays2\\[\\]\"/g, 'name="displays[]"')
                        .replace(/form-check-input flex-shrink-0/g, 'h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/30 cursor-pointer')
                        .replace(/form-check-label flex-grow-1/g, 'ml-3 flex-1 cursor-pointer select-none text-sm text-slate-700')
                        .replace(/form-check mb-0 py-1 px-4/g, 'flex items-center rounded-xl px-3 py-2 hover:bg-slate-50 transition border-b border-slate-100 last:border-0');

                    field.innerHTML = content;

                    const inputs = field.querySelectorAll('input[name="displays[]"]');
                    inputs.forEach((input, index) => {
                        if (!input.value) {
                            input.dataset.selectAll = '1';
                        } else {
                            const labelEl = input.parentElement?.querySelector('label');
                            input.dataset.label = (labelEl?.textContent || text.display).trim();
                        }

                        input.addEventListener('change', function () {
                            if (this.dataset.selectAll === '1') {
                                const checked = this.checked;
                                field.querySelectorAll('input[name="displays[]"]:not([data-select-all="1"])').forEach((item) => {
                                    item.checked = checked;
                                });
                            }
                            updateScheduleDisplaysLabel();
                        });
                    });

                    updateScheduleDisplaysLabel();
                });
        };

        function filterScheduleDisplays() {
            const query = (document.getElementById('schedule-displays-search')?.value || '').trim().toLowerCase();
            document.querySelectorAll('#schedule-displays-options > div').forEach((row) => {
                row.style.display = !query || row.textContent.toLowerCase().includes(query) ? '' : 'none';
            });
        }

        function toggleScheduleDropdown(type) {
            const panels = {
                facility: document.getElementById('schedule-facility-panel'),
                workgroup: document.getElementById('schedule-workgroup-panel'),
                workstation: document.getElementById('schedule-workstation-panel'),
            };
            scheduleHierarchyState.activeDropdown = scheduleHierarchyState.activeDropdown === type ? null : type;
            Object.entries(panels).forEach(([key, panel]) => {
                if (!panel) return;
                panel.classList.toggle('hidden', scheduleHierarchyState.activeDropdown !== key);
            });
        }

        function closeScheduleDropdowns() {
            scheduleHierarchyState.activeDropdown = null;
            ['schedule-facility-panel', 'schedule-workgroup-panel', 'schedule-workstation-panel'].forEach((id) => {
                document.getElementById(id)?.classList.add('hidden');
            });
        }

        function renderSchedulerTaskActions(row) {
            if (!canManageTasks) {
                return `<div class="scheduler-cell-action"><span class="text-xs text-slate-400">${Perfectlum.escapeHtml(text.noActions)}</span></div>`;
            }

            return `
                <div class="scheduler-cell-action">
                    <div class="relative">
                        <button type="button"
                            data-scheduler-task-toggle="${row.id}"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700">
                            <i data-lucide="more-vertical" class="h-4 w-4"></i>
                        </button>
                        <div data-scheduler-task-menu="${row.id}" class="absolute right-0 top-full z-20 mt-2 hidden w-44 overflow-hidden rounded-2xl border border-slate-200 bg-white py-1 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                            <button type="button" data-scheduler-task-edit="${row.id}" class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                                <i data-lucide="calendar-clock" class="h-4 w-4 text-sky-500"></i>
                                ${Perfectlum.escapeHtml(text.scheduleTask)}
                            </button>
                            <button type="button" data-scheduler-task-delete="${row.id}" class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                                ${Perfectlum.escapeHtml(text.deleteTask)}
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        function openSchedulerHierarchy(type, id) {
            const numericId = Number(id) || 0;
            if (!numericId) return;
            window.dispatchEvent(new CustomEvent('open-hierarchy', {
                detail: { type, id: numericId }
            }));
        }

        async function deleteSchedulerTask(id) {
            const formData = new FormData();
            formData.append('_token', @json(csrf_token()));
            formData.append('id', id);

            try {
                const payload = await Perfectlum.postForm(@json(url('delete-task')), formData);
                if (!payload.success) {
                    notify('failed', payload.msg || text.failedToDeleteTask);
                    return;
                }

                notify('success', payload.msg || text.taskDeletedSuccessfully);
                reloadSchedulerGrid();
            } catch (error) {
                notify('failed', text.failedToDeleteTask);
            }
        }

        function closeSchedulerTaskMenus() {
            document.querySelectorAll('[data-scheduler-task-menu]').forEach((menu) => menu.classList.add('hidden'));
        }

        function reloadSchedulerGrid() {
            Perfectlum.remountGrid('tasks-grid', init);
        }

        function init() {
            refreshScheduleFacilityOptions();

            document.getElementById('schedule-facility-trigger')?.addEventListener('click', () => {
                refreshScheduleFacilityOptions();
                toggleScheduleDropdown('facility');
            });
            document.getElementById('schedule-workgroup-trigger')?.addEventListener('click', () => {
                refreshScheduleWorkgroupOptions();
                toggleScheduleDropdown('workgroup');
            });
            document.getElementById('schedule-workstation-trigger')?.addEventListener('click', () => {
                refreshScheduleWorkstationOptions();
                toggleScheduleDropdown('workstation');
            });
            document.getElementById('schedule-facility-search')?.addEventListener('input', (e) => {
                scheduleHierarchyState.facilitySearch = e.target.value || '';
                refreshScheduleFacilityOptions();
            });
            document.getElementById('schedule-workgroup-search')?.addEventListener('input', (e) => {
                scheduleHierarchyState.workgroupSearch = e.target.value || '';
                refreshScheduleWorkgroupOptions();
            });
            document.getElementById('schedule-workstation-search')?.addEventListener('input', (e) => {
                scheduleHierarchyState.workstationSearch = e.target.value || '';
                refreshScheduleWorkstationOptions();
            });
            document.getElementById('schedule-displays-search')?.addEventListener('input', filterScheduleDisplays);
            document.addEventListener('click', (event) => {
                if (
                    !document.getElementById('schedule-facility-panel')?.contains(event.target) &&
                    !document.getElementById('schedule-facility-trigger')?.contains(event.target) &&
                    !document.getElementById('schedule-workgroup-panel')?.contains(event.target) &&
                    !document.getElementById('schedule-workgroup-trigger')?.contains(event.target) &&
                    !document.getElementById('schedule-workstation-panel')?.contains(event.target) &&
                    !document.getElementById('schedule-workstation-trigger')?.contains(event.target)
                ) {
                    closeScheduleDropdowns();
                }
            });

            var el = document.getElementById('tasks-grid');
            if (!el || el._gi) return;
            el._gi = true;

            Perfectlum.createGrid(el, {
                columns: [
                    {
                        name: text.task,
                        width: '280px',
                        sort: false,
                        formatter: (c) => gridjs.html(`
                            <div class="scheduler-cell">
                                <span class="text-sm font-semibold text-slate-900">${Perfectlum.escapeHtml(c || '-')}</span>
                            </div>`)
                    },
                    {
                        name: text.display,
                        sort: false,
                        width: '340px',
                        formatter: (c) => gridjs.html(`
                            <div class="scheduler-cell-stack space-y-1.5">
                                <button type="button" onclick="openSchedulerHierarchy('display', ${Number(c.displayId) || 0})" class="block text-left font-semibold text-sky-600 hover:text-sky-700 hover:underline">${Perfectlum.escapeHtml(c.displayName)}</button>
                                <div class="text-xs text-slate-500">
                                    <button type="button" onclick="openSchedulerHierarchy('workstation', ${Number(c.wsId) || 0})" class="font-semibold text-sky-600 hover:text-sky-700 hover:underline">${Perfectlum.escapeHtml(c.wsName || '-')}</button>
                                    <span class="mx-1.5">•</span>
                                    <button type="button" onclick="openSchedulerHierarchy('workgroup', ${Number(c.wgId) || 0})" class="font-semibold text-sky-600 hover:text-sky-700 hover:underline">${Perfectlum.escapeHtml(c.wgName || '-')}</button>
                                    <span class="mx-1.5">•</span>
                                    <button type="button" onclick="openSchedulerHierarchy('facility', ${Number(c.facId) || 0})" class="font-semibold text-sky-600 hover:text-sky-700 hover:underline">${Perfectlum.escapeHtml(c.facName || '-')}</button>
                                </div>
                            </div>`)
                    },
                    {
                        name: text.schedule,
                        width: '140px',
                        sort: false,
                        formatter: (c) => gridjs.html(`<div class="scheduler-cell">${Perfectlum.badge(c || '-', 'warning')}</div>`)
                    },
                    {
                        name: text.dueDate,
                        width: '160px',
                        sort: false,
                        formatter: (c, row) => {
                            const color = row.cells[1].data.dueColor;
                            const cls = { danger: 'text-red-600 font-bold', warning: 'text-amber-600 font-semibold', success: 'text-emerald-600' }[color] || 'text-slate-500';
                            return gridjs.html(`<div class="scheduler-cell"><span class="text-xs ${cls}">${Perfectlum.escapeHtml(c)}</span></div>`);
                        }
                    },
                    {
                        name: text.created,
                        width: '160px',
                        sort: false,
                        formatter: (c) => {
                            const muted = c === 'Not recorded';
                            const cls = muted ? 'text-slate-400' : 'text-slate-600';
                            const weight = muted ? 'font-medium' : 'font-semibold';
                            return gridjs.html(`<div class="scheduler-cell"><span class="text-xs ${cls} ${weight}">${Perfectlum.escapeHtml(c)}</span></div>`);
                        }
                    },
                    {
                        name: text.status,
                        width: '100px',
                        sort: false,
                        formatter: (c, row) => {
                            const ok = row.cells[1].data.statusColor === 'success';
                            const cls = ok
                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                : 'bg-red-50 text-red-700 border-red-200';
                            return gridjs.html(`<div class="scheduler-cell"><span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-bold ${cls}">${Perfectlum.escapeHtml(c)}</span></div>`);
                        }
                    },
                    {
                        name: text.enabled,
                        width: '118px',
                        sort: false,
                        formatter: (c, row) => {
                            const enabled = row.cells[1].data.enabledColor === 'success';
                            const cls = enabled
                                ? 'bg-sky-50 text-sky-700 border-sky-200'
                                : 'bg-slate-100 text-slate-600 border-slate-200';
                            return gridjs.html(`<div class="scheduler-cell"><span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-bold ${cls}">${Perfectlum.escapeHtml(c)}</span></div>`);
                        }
                    },
                    {
                        name: text.actions,
                        sort: false,
                        width: '112px',
                        formatter: (_, row) => gridjs.html(renderSchedulerTaskActions(row.cells[1].data))
                    },
                ],
                server: {
                    url: '/api/tasks?sort_mode=due{{ $selectedDisplayId ? "&display_id=$selectedDisplayId" : "" }}',
                    then: d => {
                        setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 50);
                        return d.data.map(r => [
                            r.taskName,
                            {
                                id: r.id,
                                displayId: r.displayId,
                                wsId: r.wsId,
                                wgId: r.wgId,
                                facId: r.facId,
                                displayName: r.displayName,
                                wsName: r.wsName,
                                wgName: r.wgName,
                                facName: r.facName,
                                dueColor: r.dueColor,
                                statusColor: r.statusColor,
                                enabledColor: r.enabledColor
                            },
                            r.scheduleName,
                            r.dueAt,
                            r.createdAt,
                            r.status,
                            r.enabledLabel,
                            null
                        ]);
                    },
                    total: d => d.total
                },
                pagination: {
                    enabled: true,
                    limit: 25,
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
                className: {
                    container: 'group',
                    table: 'w-full text-sm text-left',
                    thead: 'bg-slate-50/70',
                    th: 'border-b border-slate-200 bg-transparent align-middle text-xs font-black uppercase tracking-[0.22em] text-slate-400',
                    td: 'border-b border-slate-100 bg-transparent align-middle',
                    pagination: 'flex items-center justify-between px-7 py-4 text-xs font-medium text-slate-500'
                },
                language: { search: { placeholder: text.searchSchedulerTasks } }
            });
        }

        document.addEventListener('click', (event) => {
            const toggle = event.target.closest('[data-scheduler-task-toggle]');
            if (toggle) {
                event.preventDefault();
                event.stopPropagation();
                const id = toggle.dataset.schedulerTaskToggle;
                const menu = document.querySelector(`[data-scheduler-task-menu="${id}"]`);
                const willOpen = menu?.classList.contains('hidden');
                closeSchedulerTaskMenus();
                if (menu && willOpen) {
                    menu.classList.remove('hidden');
                }
                return;
            }

            const editButton = event.target.closest('[data-scheduler-task-edit]');
            if (editButton) {
                event.preventDefault();
                event.stopPropagation();
                closeSchedulerTaskMenus();
                window.edit_task(null, editButton.dataset.schedulerTaskEdit);
                return;
            }

            const deleteButton = event.target.closest('[data-scheduler-task-delete]');
            if (deleteButton) {
                event.preventDefault();
                event.stopPropagation();
                closeSchedulerTaskMenus();
                window.openTaskDeleteConfirm?.({
                    onConfirm: () => deleteSchedulerTask(deleteButton.dataset.schedulerTaskDelete)
                });
                return;
            }

            if (!event.target.closest('[data-scheduler-task-menu]')) {
                closeSchedulerTaskMenus();
            }
        });

        window.addEventListener('task-saved', reloadSchedulerGrid);

        document.readyState === 'loading'
            ? document.addEventListener('DOMContentLoaded', init)
            : init();
    })();
</script>

@include('tasks.schedule_task_modal')
@include('tasks.delete_task_confirm_modal')
@include('common.navigations.footer')
