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

    .scheduler-task-cell,
    .scheduler-display-cell {
        max-width: 100%;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .scheduler-task-title,
    .scheduler-display-title {
        display: block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.2;
        color: #0f172a;
    }

    .scheduler-task-title.is-overdue {
        color: #dc2626;
    }

    .scheduler-task-meta {
        margin-top: 0.18rem;
        display: block;
        min-width: 0;
        font-size: 12px;
        line-height: 1.55;
        color: #64748b;
        font-weight: 500;
    }

    .scheduler-display-meta {
        margin-top: 0.2rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.2rem 0.36rem;
        min-width: 0;
        font-size: 11px;
        color: #94a3b8;
    }

    .scheduler-display-meta-button {
        display: inline-flex;
        align-items: center;
        gap: 0;
        border: 0;
        background: transparent;
        padding: 0;
        font: inherit;
        font-weight: 600;
        color: #64748b;
        transition: color 160ms ease;
        min-width: 0;
        cursor: pointer;
    }

    .scheduler-display-meta-button:hover,
    .scheduler-display-meta-button:focus-visible {
        color: #0284c7;
        outline: none;
    }

    .scheduler-display-meta-badge {
        width: 0;
        height: 18px;
        border-radius: 999px;
        background: #1d9bf0;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        font-weight: 800;
        line-height: 1;
        opacity: 0;
        transform: translateX(-6px);
        overflow: hidden;
        margin-right: 0;
        transition: width 180ms ease, opacity 180ms ease, transform 180ms ease, margin-right 180ms ease;
        flex: 0 0 auto;
    }

    .scheduler-display-meta-button:hover .scheduler-display-meta-badge,
    .scheduler-display-meta-button:focus-visible .scheduler-display-meta-badge {
        width: 18px;
        opacity: 1;
        transform: translateX(0);
        margin-right: 6px;
    }

    .scheduler-display-meta-label {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .scheduler-display-separator {
        color: #cbd5e1;
        font-weight: 700;
    }

    .scheduler-table th:nth-child(3),
    .scheduler-table td:nth-child(3),
    .scheduler-table th:nth-child(6),
    .scheduler-table td:nth-child(6),
    .scheduler-table th:nth-child(7),
    .scheduler-table td:nth-child(7) {
        display: none;
    }

    .scheduler-create-shell {
        border-radius: 1.5rem;
        border: 1px solid #dce8f4;
        background: linear-gradient(180deg, #f9fcff 0%, #ffffff 100%);
        box-shadow: 0 14px 38px -28px rgba(15, 23, 42, 0.22);
    }

    .scheduler-jobs-shell {
        border-radius: 2rem;
        border: 1px solid #d5e0ec;
        background: linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
        box-shadow: 0 26px 64px -46px rgba(15, 23, 42, 0.34);
        overflow: hidden;
    }

    .scheduler-jobs-head {
        padding: 18px 18px 12px;
    }

    .scheduler-table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 18px;
        border-top: 1px solid #e3ecf5;
        border-bottom: 1px solid #e3ecf5;
        background: #f8fbff;
    }

    .scheduler-table-search {
        width: min(440px, 100%);
        height: 42px;
        border-radius: 999px;
        border: 1px solid #c9d8e8;
        padding: 0 16px;
        font-size: 14px;
        font-weight: 600;
        color: #12263a;
        background: #fff;
    }

    .scheduler-table-search:focus {
        outline: none;
        border-color: #1d9bf0;
        box-shadow: 0 0 0 3px rgba(29, 155, 240, 0.16);
    }

    .scheduler-table-wrap {
        overflow-x: auto;
        background: #fff;
    }

    .scheduler-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 1160px;
        table-layout: fixed;
    }

    .scheduler-table th {
        padding: 13px 16px;
        text-align: left;
        border-bottom: 1px solid #d8e4f0;
        background: #e9f1fa;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: #4d647d;
        white-space: nowrap;
    }

    .scheduler-sort-btn {
        border: 0;
        background: transparent;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #5a7087;
        font: inherit;
        letter-spacing: inherit;
        text-transform: inherit;
        cursor: pointer;
        border-radius: 999px;
        padding: 2px 8px;
        margin-left: -8px;
        transition: all .18s ease;
    }

    .scheduler-sort-btn:hover {
        background: #f2f7fd;
        color: #2f4d6a;
    }

    .scheduler-sort-btn.is-active {
        background: #e2edf9;
        color: #24486b;
    }

    .scheduler-sort-indicator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 999px;
        background: #d4e3f4;
        color: #2f5477;
        font-size: 10px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: 0;
        transition: all .18s ease;
    }

    .scheduler-sort-btn.is-active .scheduler-sort-indicator {
        background: #2f6fae;
        color: #ffffff;
    }

    .scheduler-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #edf2f8;
        font-size: 14px;
        color: #334155;
        vertical-align: middle;
        background: #fff;
    }

    .scheduler-table tbody tr:hover td {
        background: #f7fbff;
    }

    .scheduler-table th:nth-child(1),
    .scheduler-table td:nth-child(1) { width: 30%; }
    .scheduler-table th:nth-child(2),
    .scheduler-table td:nth-child(2) { width: 38%; }
    .scheduler-table th:nth-child(4),
    .scheduler-table td:nth-child(4) { width: 12%; text-align: center; }
    .scheduler-table th:nth-child(5),
    .scheduler-table td:nth-child(5) { width: 12%; text-align: center; }
    .scheduler-table th:nth-child(8),
    .scheduler-table td:nth-child(8) { width: 8%; text-align: center; }

    .scheduler-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 16px 14px;
        border-top: 1px solid #dbe7f3;
        background: #f7fbff;
    }

    .scheduler-pager {
        display: inline-flex;
        gap: 8px;
        align-items: center;
    }

    .scheduler-page-btn {
        height: 32px;
        min-width: 32px;
        border-radius: 999px;
        border: 1px solid #c7d6e7;
        background: #ffffff;
        color: #2c4158;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        padding: 0 12px;
        transition: all .18s ease;
    }

    .scheduler-page-btn:hover:not(:disabled) {
        border-color: #1d9bf0;
        color: #0f5f9f;
        background: #f0f8ff;
    }

    .scheduler-page-btn:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .scheduler-empty {
        padding: 24px 16px;
        text-align: center;
        color: #5f7388;
        font-size: 14px;
        border-bottom: 1px solid #edf2f8;
    }

    @media (max-width: 768px) {
        .scheduler-table-toolbar {
            flex-direction: column;
            align-items: stretch;
        }
        .scheduler-table-search {
            width: 100%;
        }
        .scheduler-table-footer {
            flex-direction: column;
            align-items: stretch;
        }
        .scheduler-pager {
            justify-content: flex-end;
        }
    }
</style>

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
            <div class="scheduler-create-shell mb-6 p-5"
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

            <div class="scheduler-jobs-shell">
                <div class="scheduler-jobs-head">
                    <div class="space-y-2">
                        <p class="text-[11px] font-black uppercase tracking-[0.28em] text-slate-400">{{ __('Scheduled Tasks') }}</p>
                        <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ __('All calibration and QA schedules') }}</h2>
                        <p class="mt-1 max-w-3xl text-sm text-slate-500">{{ __('Nearest due schedules are shown first so upcoming work is easier to prioritize.') }}</p>
                    </div>
                </div>
                <div class="scheduler-table-toolbar">
                    <input id="scheduler-table-search" type="text" class="scheduler-table-search transition-all placeholder-gray-400" placeholder="{{ __('Search scheduler tasks...') }}">
                    <div id="scheduler-table-meta" class="text-[12px] font-semibold text-slate-500"></div>
                </div>
                <div class="scheduler-table-wrap">
                    <table class="scheduler-table">
                        <thead>
                            <tr>
                                <th><button type="button" data-scheduler-sort="task_name" class="scheduler-sort-btn"><span>{{ __('Task') }}</span><span class="scheduler-sort-indicator" data-scheduler-sort-indicator="task_name">↕</span></button></th>
                                <th><button type="button" data-scheduler-sort="display_name" class="scheduler-sort-btn"><span>{{ __('Display') }}</span><span class="scheduler-sort-indicator" data-scheduler-sort-indicator="display_name">↕</span></button></th>
                                <th><button type="button" data-scheduler-sort="schedule_name" class="scheduler-sort-btn"><span>{{ __('Schedule') }}</span><span class="scheduler-sort-indicator" data-scheduler-sort-indicator="schedule_name">↕</span></button></th>
                                <th><button type="button" data-scheduler-sort="due_at" class="scheduler-sort-btn"><span>{{ __('Due Date') }}</span><span class="scheduler-sort-indicator" data-scheduler-sort-indicator="due_at">↕</span></button></th>
                                <th><button type="button" data-scheduler-sort="created_at" class="scheduler-sort-btn"><span>{{ __('Created') }}</span><span class="scheduler-sort-indicator" data-scheduler-sort-indicator="created_at">↕</span></button></th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Enabled') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="scheduler-tasks-body"></tbody>
                    </table>
                </div>
                <div class="scheduler-table-footer">
                    <div id="scheduler-table-summary" class="text-[12px] font-semibold text-slate-500"></div>
                    <div class="scheduler-pager">
                        <button id="scheduler-page-prev" type="button" class="scheduler-page-btn">{{ __('Previous') }}</button>
                        <span id="scheduler-page-label" class="text-[12px] font-semibold text-slate-500"></span>
                        <button id="scheduler-page-next" type="button" class="scheduler-page-btn">{{ __('Next') }}</button>
                    </div>
                </div>
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
        const schedulerTableState = {
            page: 1,
            limit: 25,
            total: 0,
            loading: false,
            fetching: false,
            search: '',
            searchTimer: null,
            rows: [],
            sortKey: '',
            sortOrder: 'asc',
            autoRefreshTimer: null,
            autoRefreshIntervalVisibleMs: 15000,
            autoRefreshIntervalHiddenMs: 45000,
            autoRefreshTicks: 0,
        };

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
            schedulerTableState.autoRefreshTicks = 0;
            loadSchedulerTasks({ withoutTotal: false, silent: false });
            queueSchedulerAutoRefresh();
        }

        function schedulerTasksUrl(options = {}) {
            const withoutTotal = Boolean(options.withoutTotal);
            const url = new URL('/api/tasks', window.location.origin);
            if (schedulerTableState.sortKey) {
                url.searchParams.set('sort', schedulerTableState.sortKey);
                url.searchParams.set('order', schedulerTableState.sortOrder);
            } else {
                url.searchParams.set('sort_mode', 'due');
            }
            @if($selectedDisplayId)
            url.searchParams.set('display_id', '{{ $selectedDisplayId }}');
            @endif
            url.searchParams.set('page', String(schedulerTableState.page));
            url.searchParams.set('limit', String(schedulerTableState.limit));
            if (schedulerTableState.search) {
                url.searchParams.set('search', schedulerTableState.search);
            }
            if (withoutTotal) {
                url.searchParams.set('without_total', '1');
            }
            return `${url.pathname}${url.search}`;
        }

        function renderSchedulerDisplayCell(row) {
            return `
                <div class="scheduler-cell-stack space-y-1.5">
                    <button type="button" data-scheduler-open="display" data-scheduler-id="${Number(row.displayId) || 0}" class="block text-left font-semibold text-sky-600 hover:text-sky-700 hover:underline">${Perfectlum.escapeHtml(row.displayName || '-')}</button>
                    <div class="text-xs text-slate-500">
                        <button type="button" data-scheduler-open="workstation" data-scheduler-id="${Number(row.wsId) || 0}" class="font-semibold text-sky-600 hover:text-sky-700 hover:underline">${Perfectlum.escapeHtml(row.wsName || '-')}</button>
                        <span class="mx-1.5">•</span>
                        <button type="button" data-scheduler-open="workgroup" data-scheduler-id="${Number(row.wgId) || 0}" class="font-semibold text-sky-600 hover:text-sky-700 hover:underline">${Perfectlum.escapeHtml(row.wgName || '-')}</button>
                        <span class="mx-1.5">•</span>
                        <button type="button" data-scheduler-open="facility" data-scheduler-id="${Number(row.facId) || 0}" class="font-semibold text-sky-600 hover:text-sky-700 hover:underline">${Perfectlum.escapeHtml(row.facName || '-')}</button>
                    </div>
                </div>
            `;
        }

        function renderSchedulerRows() {
            const body = document.getElementById('scheduler-tasks-body');
            if (!body) return;

            if (schedulerTableState.loading) {
                body.innerHTML = `<tr><td colspan="8" class="scheduler-empty">Loading...</td></tr>`;
                return;
            }

            if (!schedulerTableState.rows.length) {
                body.innerHTML = `<tr><td colspan="8" class="scheduler-empty">No matching records found</td></tr>`;
                return;
            }

            body.innerHTML = schedulerTableState.rows.map((row) => {
                const dueCls = { danger: 'text-red-600 font-bold', warning: 'text-amber-600 font-semibold', success: 'text-emerald-600' }[row.dueColor] || 'text-slate-500';
                const createdMuted = row.createdAt === 'Not recorded';
                const createdCls = createdMuted ? 'text-slate-400 font-medium' : 'text-slate-600 font-semibold';
                const statusCls = row.statusColor === 'success'
                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                    : 'bg-red-50 text-red-700 border-red-200';
                const enabledCls = row.enabledColor === 'success'
                    ? 'bg-sky-50 text-sky-700 border-sky-200'
                    : 'bg-slate-100 text-slate-600 border-slate-200';

                return `
                    <tr>
                        <td><div class="scheduler-cell"><span class="text-sm font-semibold text-slate-900">${Perfectlum.escapeHtml(row.taskName || '-')}</span></div></td>
                        <td>${renderSchedulerDisplayCell(row)}</td>
                        <td><div class="scheduler-cell">${Perfectlum.badge(row.scheduleName || '-', 'warning')}</div></td>
                        <td><div class="scheduler-cell"><span class="text-xs ${dueCls}">${Perfectlum.escapeHtml(row.dueAt || '-')}</span></div></td>
                        <td><div class="scheduler-cell"><span class="text-xs ${createdCls}">${Perfectlum.escapeHtml(row.createdAt || 'Not recorded')}</span></div></td>
                        <td><div class="scheduler-cell"><span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-bold ${statusCls}">${Perfectlum.escapeHtml(row.status || '-')}</span></div></td>
                        <td><div class="scheduler-cell"><span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-bold ${enabledCls}">${Perfectlum.escapeHtml(row.enabledLabel || '-')}</span></div></td>
                        <td>${renderSchedulerTaskActions(row)}</td>
                    </tr>
                `;
            }).join('');

            window.lucide?.createIcons();
        }

        function renderSchedulerMetaButton(type, id, label, badge) {
            const safeLabel = String(label || '').trim();
            const numericId = Number(id) || 0;
            if (!numericId || !safeLabel || safeLabel === '-') {
                return '';
            }

            return `
                <button type="button" data-scheduler-open="${Perfectlum.escapeHtml(type)}" data-scheduler-id="${numericId}" class="scheduler-display-meta-button">
                    <span class="scheduler-display-meta-badge">${Perfectlum.escapeHtml(badge)}</span>
                    <span class="scheduler-display-meta-label">${Perfectlum.escapeHtml(safeLabel)}</span>
                </button>
            `;
        }

        function renderSchedulerDisplayCell(row) {
            const facility = renderSchedulerMetaButton('facility', row.facId, row.facName, 'F');
            const workgroup = renderSchedulerMetaButton('workgroup', row.wgId, row.wgName, 'WG');
            const workstation = renderSchedulerMetaButton('workstation', row.wsId, row.wsName, 'WS');
            const meta = [facility, workgroup, workstation]
                .filter(Boolean)
                .join('<span class="scheduler-display-separator">•</span>');

            return `
                <div class="scheduler-display-cell">
                    <button type="button" data-scheduler-open="display" data-scheduler-id="${Number(row.displayId) || 0}" class="scheduler-display-title text-left transition hover:text-sky-600 hover:underline">${Perfectlum.escapeHtml(row.displayName || '-')}</button>
                    <div class="scheduler-display-meta">${meta}</div>
                </div>
            `;
        }

        function renderSchedulerRows() {
            const body = document.getElementById('scheduler-tasks-body');
            if (!body) return;

            if (schedulerTableState.loading) {
                body.innerHTML = `<tr><td colspan="8" class="scheduler-empty">Loading...</td></tr>`;
                return;
            }

            if (!schedulerTableState.rows.length) {
                body.innerHTML = `<tr><td colspan="8" class="scheduler-empty">No matching records found</td></tr>`;
                return;
            }

            body.innerHTML = schedulerTableState.rows.map((row) => {
                const dueCls = { danger: 'text-red-600 font-bold', warning: 'text-amber-600 font-semibold', success: 'text-emerald-600' }[row.dueColor] || 'text-slate-500';
                const createdMuted = row.createdAt === 'Not recorded';
                const createdCls = createdMuted ? 'text-slate-400 font-medium' : 'text-slate-600 font-semibold';
                const taskTitleCls = row.dueColor === 'danger' ? 'is-overdue' : '';
                const taskMeta = [
                    `Status : ${row.status || '-'}`,
                    `Type : ${row.scheduleName || '-'}`,
                    `${row.enabledLabel || '-'}`
                ].join(' | ');

                return `
                    <tr>
                        <td>
                            <div class="scheduler-task-cell">
                                <span class="scheduler-task-title ${taskTitleCls}">${Perfectlum.escapeHtml(row.taskName || '-')}</span>
                                <div class="scheduler-task-meta">${Perfectlum.escapeHtml(taskMeta)}</div>
                            </div>
                        </td>
                        <td>${renderSchedulerDisplayCell(row)}</td>
                        <td class="hidden"></td>
                        <td><div class="scheduler-cell justify-center"><span class="text-xs ${dueCls}">${Perfectlum.escapeHtml(row.dueAt || '-')}</span></div></td>
                        <td><div class="scheduler-cell justify-center"><span class="text-xs ${createdCls}">${Perfectlum.escapeHtml(row.createdAt || 'Not recorded')}</span></div></td>
                        <td class="hidden"></td>
                        <td class="hidden"></td>
                        <td>${renderSchedulerTaskActions(row)}</td>
                    </tr>
                `;
            }).join('');

            window.lucide?.createIcons();
        }

        function renderSchedulerPager() {
            const totalPages = Math.max(1, Math.ceil(schedulerTableState.total / schedulerTableState.limit));
            const currentPage = Math.min(schedulerTableState.page, totalPages);
            const from = schedulerTableState.total === 0 ? 0 : ((currentPage - 1) * schedulerTableState.limit) + 1;
            const to = Math.min(schedulerTableState.total, currentPage * schedulerTableState.limit);

            const meta = document.getElementById('scheduler-table-meta');
            const summary = document.getElementById('scheduler-table-summary');
            const label = document.getElementById('scheduler-page-label');
            const prev = document.getElementById('scheduler-page-prev');
            const next = document.getElementById('scheduler-page-next');

            if (meta) meta.textContent = `${schedulerTableState.total} results`;
            if (summary) summary.textContent = `Showing ${from}-${to} of ${schedulerTableState.total} results`;
            if (label) label.textContent = `Page ${currentPage} / ${totalPages}`;
            if (prev) prev.disabled = schedulerTableState.loading || schedulerTableState.fetching || currentPage <= 1;
            if (next) next.disabled = schedulerTableState.loading || schedulerTableState.fetching || currentPage >= totalPages;
        }

        function updateSchedulerSortIndicators() {
            document.querySelectorAll('[data-scheduler-sort]').forEach((button) => {
                const key = button.getAttribute('data-scheduler-sort');
                button.classList.toggle('is-active', key === schedulerTableState.sortKey);
            });
            document.querySelectorAll('[data-scheduler-sort-indicator]').forEach((node) => {
                const key = node.getAttribute('data-scheduler-sort-indicator');
                if (key === schedulerTableState.sortKey) {
                    node.textContent = schedulerTableState.sortOrder === 'asc' ? '↑' : '↓';
                } else {
                    node.textContent = '↕';
                }
            });
        }

        function queueSchedulerAutoRefresh() {
            if (schedulerTableState.autoRefreshTimer) {
                clearTimeout(schedulerTableState.autoRefreshTimer);
                schedulerTableState.autoRefreshTimer = null;
            }
            const interval = document.visibilityState === 'visible'
                ? schedulerTableState.autoRefreshIntervalVisibleMs
                : schedulerTableState.autoRefreshIntervalHiddenMs;

            schedulerTableState.autoRefreshTimer = window.setTimeout(async () => {
                if (!document.body?.contains(document.getElementById('scheduler-tasks-body'))) return;
                const shouldIncludeTotal = schedulerTableState.autoRefreshTicks % 4 === 0;
                schedulerTableState.autoRefreshTicks += 1;
                await loadSchedulerTasks({
                    withoutTotal: !shouldIncludeTotal,
                    silent: true,
                });
                queueSchedulerAutoRefresh();
            }, interval);
        }

        window.schedulerPageCleanup = function () {
            if (schedulerTableState.autoRefreshTimer) {
                clearTimeout(schedulerTableState.autoRefreshTimer);
                schedulerTableState.autoRefreshTimer = null;
            }
            schedulerTableState.fetching = false;
            schedulerTableState.loading = false;
        };

        async function loadSchedulerTasks(options = {}) {
            const withoutTotal = Boolean(options.withoutTotal);
            const silent = Boolean(options.silent);
            if (schedulerTableState.fetching) return;

            schedulerTableState.fetching = true;
            if (!silent) {
                schedulerTableState.loading = true;
                renderSchedulerPager();
                renderSchedulerRows();
            }

            try {
                const payload = await Perfectlum.request(schedulerTasksUrl({ withoutTotal }));
                schedulerTableState.rows = Array.isArray(payload?.data) ? payload.data : [];
                if (typeof payload?.total === 'number' && Number.isFinite(payload.total)) {
                    schedulerTableState.total = Number(payload.total);
                } else if (!withoutTotal) {
                    schedulerTableState.total = Number(schedulerTableState.rows.length || 0);
                }

                const totalPages = Math.max(1, Math.ceil(schedulerTableState.total / schedulerTableState.limit));
                if (schedulerTableState.page > totalPages) {
                    schedulerTableState.page = totalPages;
                    return loadSchedulerTasks({ withoutTotal: false, silent: false });
                }
            } catch (error) {
                schedulerTableState.rows = [];
                schedulerTableState.total = 0;
                const body = document.getElementById('scheduler-tasks-body');
                if (body) {
                    body.innerHTML = `<tr><td colspan="8" class="scheduler-empty text-rose-600">${Perfectlum.escapeHtml(error.message || 'Unable to load data')}</td></tr>`;
                }
            } finally {
                schedulerTableState.fetching = false;
                schedulerTableState.loading = false;
                renderSchedulerRows();
                renderSchedulerPager();
            }
        }

        function init() {
            const body = document.getElementById('scheduler-tasks-body');
            if (!body || body.dataset.schedulerInitialized === '1') {
                return;
            }
            body.dataset.schedulerInitialized = '1';

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

            const searchInput = document.getElementById('scheduler-table-search');
            searchInput?.addEventListener('input', (event) => {
                clearTimeout(schedulerTableState.searchTimer);
                schedulerTableState.searchTimer = window.setTimeout(() => {
                    schedulerTableState.search = String(event.target.value || '').trim();
                    schedulerTableState.page = 1;
                    schedulerTableState.autoRefreshTicks = 0;
                    loadSchedulerTasks({ withoutTotal: false, silent: false });
                    queueSchedulerAutoRefresh();
                }, 350);
            });

            document.querySelectorAll('[data-scheduler-sort]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    const key = button.getAttribute('data-scheduler-sort');
                    if (!key) return;
                    if (schedulerTableState.sortKey === key) {
                        schedulerTableState.sortOrder = schedulerTableState.sortOrder === 'asc' ? 'desc' : 'asc';
                    } else {
                        schedulerTableState.sortKey = key;
                        schedulerTableState.sortOrder = (key === 'due_at' || key === 'created_at') ? 'desc' : 'asc';
                    }
                    schedulerTableState.page = 1;
                    updateSchedulerSortIndicators();
                    schedulerTableState.autoRefreshTicks = 0;
                    loadSchedulerTasks({ withoutTotal: false, silent: false });
                    queueSchedulerAutoRefresh();
                });
            });

            document.getElementById('scheduler-page-prev')?.addEventListener('click', () => {
                if (schedulerTableState.page <= 1 || schedulerTableState.loading || schedulerTableState.fetching) return;
                schedulerTableState.page -= 1;
                schedulerTableState.autoRefreshTicks = 0;
                loadSchedulerTasks({ withoutTotal: false, silent: false });
                queueSchedulerAutoRefresh();
            });

            document.getElementById('scheduler-page-next')?.addEventListener('click', () => {
                const totalPages = Math.max(1, Math.ceil(schedulerTableState.total / schedulerTableState.limit));
                if (schedulerTableState.page >= totalPages || schedulerTableState.loading || schedulerTableState.fetching) return;
                schedulerTableState.page += 1;
                schedulerTableState.autoRefreshTicks = 0;
                loadSchedulerTasks({ withoutTotal: false, silent: false });
                queueSchedulerAutoRefresh();
            });

            updateSchedulerSortIndicators();
            loadSchedulerTasks({ withoutTotal: false, silent: false });
            queueSchedulerAutoRefresh();
            document.addEventListener('visibilitychange', queueSchedulerAutoRefresh);
            window.addEventListener('focus', queueSchedulerAutoRefresh);
            window.addEventListener('beforeunload', () => {
                if (schedulerTableState.autoRefreshTimer) {
                    clearTimeout(schedulerTableState.autoRefreshTimer);
                    schedulerTableState.autoRefreshTimer = null;
                }
            });

            if (false) {
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
        }

        document.addEventListener('click', (event) => {
            const openButton = event.target.closest('[data-scheduler-open]');
            if (openButton) {
                event.preventDefault();
                event.stopPropagation();
                openSchedulerHierarchy(openButton.dataset.schedulerOpen, openButton.dataset.schedulerId);
                return;
            }

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

        if (window.__schedulerTaskSavedHandler) {
            window.removeEventListener('task-saved', window.__schedulerTaskSavedHandler);
        }
        window.__schedulerTaskSavedHandler = reloadSchedulerGrid;
        window.addEventListener('task-saved', window.__schedulerTaskSavedHandler);

        window.schedulerPageMount = function () {
            const body = document.getElementById('scheduler-tasks-body');
            if (body) {
                delete body.dataset.schedulerInitialized;
            }
            init();
        };

        document.readyState === 'loading'
            ? document.addEventListener('DOMContentLoaded', init)
            : init();
    })();
</script>

@include('tasks.schedule_task_modal')
@include('tasks.delete_task_confirm_modal')
@include('common.navigations.footer')
