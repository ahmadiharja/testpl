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
    }

    #tasks-grid .scheduler-cell-action > button {
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

    .scheduler-task-title-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        min-width: 0;
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

    .scheduler-task-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.1;
        white-space: nowrap;
        text-transform: none;
        flex: 0 0 auto;
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .scheduler-task-pill--startup {
        border-color: #cbd5e1;
        background: #f8fafc;
        color: #475569;
    }

    .scheduler-task-pill--once {
        border-color: #ddd6fe;
        background: #f5f3ff;
        color: #6d28d9;
    }

    .scheduler-task-pill--daily {
        border-color: #bae6fd;
        background: #f0f9ff;
        color: #0369a1;
    }

    .scheduler-task-pill--weekly {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #15803d;
    }

    .scheduler-task-pill--monthly {
        border-color: #fde68a;
        background: #fffbeb;
        color: #b45309;
    }

    .scheduler-task-pill--quarterly {
        border-color: #fecaca;
        background: #fef2f2;
        color: #b91c1c;
    }

    .scheduler-task-pill--semiannually {
        border-color: #fbcfe8;
        background: #fdf2f8;
        color: #be185d;
    }

    .scheduler-task-pill--annually {
        border-color: #bfdbfe;
        background: #eef2ff;
        color: #4338ca;
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
        position: relative;
        overflow: visible;
        isolation: isolate;
        z-index: 60;
        border-radius: 1.5rem;
        border: 1px solid #dce8f4;
        background: linear-gradient(180deg, #f9fcff 0%, #ffffff 100%);
        box-shadow: 0 14px 38px -28px rgba(15, 23, 42, 0.22);
    }

    .scheduler-hierarchy-field {
        position: relative;
        z-index: 10;
    }

    .scheduler-hierarchy-field:focus-within {
        z-index: 120;
    }

    .scheduler-hierarchy-panel {
        z-index: 140;
    }

    .scheduler-jobs-shell {
        position: relative;
        z-index: 1;
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
        overflow-y: hidden;
        background: #fff;
        position: relative;
        z-index: 1;
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

    .scheduler-floating-menu {
        position: fixed;
        z-index: 29950;
        width: 11rem;
        overflow: hidden;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 0.25rem 0;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.14);
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
                    @click="activateCalendar()"
                    class="rounded-t-2xl px-4 py-3 text-sm font-semibold transition"
                    :class="activeTab === 'calendar' ? 'border-b-2 border-sky-500 text-sky-600' : 'text-slate-500 hover:text-slate-700'">
                    {{ __('Calendar') }}
                </button>
            </div>
        </div>

        <div x-show="activeTab === 'tasks'" x-cloak>
            @if($canManageSchedulerDesktop)
            <div class="scheduler-create-shell mb-6 p-5">
                <div class="mb-4">
                    <p class="text-[11px] font-black uppercase tracking-[0.28em] text-slate-400">{{ __('Create Schedule') }}</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ __('Schedule tasks by hierarchy') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ __('Pick a facility, workgroup, workstation, and optional displays, then continue to the schedule editor.') }}</p>
                </div>

                <form id="scheduler-create-form" onsubmit="event.preventDefault(); window.create_task(this)">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
                        <label class="block scheduler-hierarchy-field">
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
                                <button type="button" id="schedule-facility-trigger" onclick="window.openScheduleHierarchyDropdown && window.openScheduleHierarchyDropdown('facility')"
                                    class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-900 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                    <span id="schedule-facility-label" class="truncate">{{ __('Please select') }}</span>
                                    <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                                </button>
                                <div id="schedule-facility-panel" class="scheduler-hierarchy-panel absolute left-0 right-0 top-[calc(100%+0.5rem)] hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                                    <input id="schedule-facility-search" type="text" placeholder="{{ __('Search facilities...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                    <p id="schedule-facility-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                                    <div id="schedule-facility-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                                </div>
                            </div>
                        </label>

                        <label class="block scheduler-hierarchy-field">
                            <span class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Workgroup') }}</span>
                            <select name="workgroup2" id="schedule_workgroups_field"
                                onchange="fetch_schedule_workstations(this)"
                                class="hidden">
                                <option value="">{{ __('Select facility first') }}</option>
                            </select>
                            <div class="relative">
                                <button type="button" id="schedule-workgroup-trigger" onclick="window.openScheduleHierarchyDropdown && window.openScheduleHierarchyDropdown('workgroup')"
                                    class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-900 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                                    <span id="schedule-workgroup-label" class="truncate">{{ __('Select facility first') }}</span>
                                    <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                                </button>
                                <div id="schedule-workgroup-panel" class="scheduler-hierarchy-panel absolute left-0 right-0 top-[calc(100%+0.5rem)] hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                                    <input id="schedule-workgroup-search" type="text" placeholder="{{ __('Search workgroups...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                    <p id="schedule-workgroup-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                                    <div id="schedule-workgroup-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                                </div>
                            </div>
                        </label>

                        <label class="block scheduler-hierarchy-field">
                            <span class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Workstation') }}</span>
                            <select name="workstation2" id="schedule_workstations_field"
                                onchange="fetch_schedule_displays(this)"
                                class="hidden">
                                <option value="">{{ __('Select workgroup first') }}</option>
                            </select>
                            <div class="relative">
                                <button type="button" id="schedule-workstation-trigger" onclick="window.openScheduleHierarchyDropdown && window.openScheduleHierarchyDropdown('workstation')"
                                    class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-900 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                                    <span id="schedule-workstation-label" class="truncate">{{ __('Select workgroup first') }}</span>
                                    <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                                </button>
                                <div id="schedule-workstation-panel" class="scheduler-hierarchy-panel absolute left-0 right-0 top-[calc(100%+0.5rem)] hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                                    <input id="schedule-workstation-search" type="text" placeholder="{{ __('Search workstations...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                    <p id="schedule-workstation-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                                    <div id="schedule-workstation-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                                </div>
                            </div>
                        </label>

                        <div class="relative scheduler-hierarchy-field">
                            <span class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Displays') }}</span>
                            <button type="button" onclick="window.openScheduleHierarchyDropdown && window.openScheduleHierarchyDropdown('displays')"
                                id="schedule_displays_dropdown"
                                class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-900 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                <span id="schedule_displays_label" class="truncate">{{ __('Select workstation first') }}</span>
                                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-200"></i>
                            </button>
                            <div id="schedule_displays_field"
                                class="scheduler-hierarchy-panel absolute left-0 right-0 mt-2 max-h-72 overflow-auto rounded-[1.25rem] border border-slate-200 bg-white p-2 shadow-[0_20px_45px_rgba(15,23,42,0.14)]"
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
                            <button type="submit" id="schedule-submit-btn" disabled
                                class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-full bg-sky-500 px-6 text-sm font-semibold text-white shadow-[0_16px_30px_rgba(14,165,233,0.24)] transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-white/80 disabled:shadow-none">
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

<div x-data="schedulerCalendarEventModal()"
    x-init="mount($root)"
    @scheduler-event.window="openFromEvent($event.detail)"
    class="hidden">
    <div x-show="open" x-cloak class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" style="z-index: 49990;"></div>
    <div x-show="open" x-cloak class="fixed inset-0 flex items-center justify-center p-4" style="z-index: 50000;">
        <div @click.away="close()"
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
                <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4">
                    <p class="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Schedule') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900" x-text="event?.scheduleLabel || '-'"></p>
                </div>
                <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4">
                    <p class="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Last Execution') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900" x-text="event?.lastRunLabel || '-'"></p>
                </div>
                <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4 md:col-span-2">
                    <p class="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Location') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900" x-text="event?.locationLabel || '-'"></p>
                </div>
            </div>
            <div class="border-t border-slate-200 px-6 py-5">
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-5 py-4">
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Latest Synced Result') }}</p>
                            <template x-if="event?.historySummary">
                                <div class="mt-2 space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-bold"
                                            :class="resultToneClass(event?.historySummary?.resultTone)"
                                            x-text="event?.historySummary?.resultLabel || '-'"></span>
                                        <span class="text-sm font-semibold text-slate-900" x-text="event?.historySummary?.name || '-'"></span>
                                    </div>
                                    <p class="text-sm text-slate-500" x-text="event?.historySummary?.performedAt || '-'"></p>
                                </div>
                            </template>
                            <template x-if="!event?.historySummary">
                                <p class="mt-2 text-sm text-slate-500">{{ __('No synced result has been recorded for this display yet.') }}</p>
                            </template>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button"
                                x-show="event?.historySummary?.reportUrl"
                                @click="openUrl(event?.historySummary?.reportUrl)"
                                class="inline-flex h-10 items-center justify-center rounded-full border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                                {{ __('Open Report') }}
                            </button>
                            <button type="button"
                                x-show="event?.historyReportsUrl"
                                @click="openUrl(event?.historyReportsUrl)"
                                class="inline-flex h-10 items-center justify-center rounded-full border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                                {{ __('History List') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                <button type="button" x-show="event?.displayCalibrationUrl" @click="openUrl(event?.displayCalibrationUrl)"
                    class="inline-flex h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                    {{ __('Open Display') }}
                </button>
                <button type="button" @click="close()"
                    class="inline-flex h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function schedulerCalendarEventModal() {
        return {
            open: false,
            event: null,
            mount(root) {
                if (root && root.parentElement !== document.body) {
                    document.body.appendChild(root);
                }
                this.toggleShellScroll(false);
                this.$watch('open', (value) => {
                    this.toggleShellScroll(value);
                });
            },
            openFromEvent(event) {
                this.event = event || null;
                this.open = true;
            },
            close() {
                this.open = false;
            },
            openUrl(url) {
                if (!url) return;
                window.location.href = url;
            },
            resultToneClass(tone) {
                return {
                    success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
                    danger: 'border-rose-200 bg-rose-50 text-rose-700',
                    warning: 'border-amber-200 bg-amber-50 text-amber-700',
                    neutral: 'border-slate-200 bg-white text-slate-700',
                }[tone || 'neutral'] || 'border-slate-200 bg-white text-slate-700';
            },
            toggleShellScroll(locked) {
                const method = locked ? 'add' : 'remove';
                document.documentElement.classList[method]('overflow-hidden');
                document.body.classList[method]('overflow-hidden');
                document.getElementById('desktop-page-stage')?.classList[method]('overflow-hidden');
                document.getElementById('desktop-scroll-area')?.classList[method]('overflow-hidden');
            }
        };
    }

    function schedulerPage() {
        return {
            activeTab: 'tasks',
            calendarReady: false,
            calendarInstance: null,
            activateCalendar() {
                this.activeTab = 'calendar';
                this.$nextTick(() => {
                    this.initCalendarIfNeeded();
                });
            },
            async initCalendarIfNeeded() {
                if (this.calendarReady) {
                    this.$nextTick(() => {
                        requestAnimationFrame(() => {
                            this.calendarInstance?.updateSize?.();
                            this.calendarInstance?.refetchEvents?.();
                        });
                    });
                    return;
                }
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

                requestAnimationFrame(() => {
                    this.calendarInstance?.updateSize?.();
                    this.calendarInstance?.refetchEvents?.();
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
            fetchController: null,
            totalSyncTimer: null,
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

        function ensureSchedulerDesktopMenuState() {
            const appRoot = document.querySelector('body[x-data]');
            const alpineData = window.Alpine?.$data ? window.Alpine.$data(appRoot) : appRoot?.__x?.$data;
            if (!alpineData) {
                return;
            }

            alpineData.activeMenu = 'Scheduler';
            alpineData.settingsExpanded = false;
        }

        function updateScheduleSubmitState() {
            const button = document.getElementById('schedule-submit-btn');
            if (!button) return;
            const facility = document.getElementById('schedule_facility_field')?.value || '';
            const workgroup = document.getElementById('schedule_workgroups_field')?.value || '';
            const workstation = document.getElementById('schedule_workstations_field')?.value || '';
            const field = document.getElementById('schedule-displays-options');
            let hasDisplay = false;
            if (field) {
                const regular = Array.from(field.querySelectorAll('input[name="displays[]"]:not([data-select-all="1"])'));
                const selected = regular.filter((item) => item.checked);
                const selectAll = field.querySelector('input[name="displays[]"][data-select-all="1"]');
                hasDisplay = selected.length > 0 || (selectAll && selectAll.checked && regular.length > 0);
            }
            button.disabled = !(facility && workgroup && workstation && hasDisplay);
        }

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
            updateScheduleSubmitState();
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
            updateScheduleSubmitState();
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
                    updateScheduleSubmitState();
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
                    updateScheduleSubmitState();
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
                    updateScheduleSubmitState();
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
                displays: document.getElementById('schedule_displays_field'),
            };
            scheduleHierarchyState.activeDropdown = scheduleHierarchyState.activeDropdown === type ? null : type;
            Object.entries(panels).forEach(([key, panel]) => {
                if (!panel) return;
                const shouldHide = scheduleHierarchyState.activeDropdown !== key;
                panel.classList.toggle('hidden', shouldHide);
                if (key === 'displays') {
                    panel.style.display = shouldHide ? 'none' : '';
                }
            });
        }

        function closeScheduleDropdowns() {
            scheduleHierarchyState.activeDropdown = null;
            ['schedule-facility-panel', 'schedule-workgroup-panel', 'schedule-workstation-panel', 'schedule_displays_field'].forEach((id) => {
                const node = document.getElementById(id);
                node?.classList.add('hidden');
                if (id === 'schedule_displays_field' && node) {
                    node.style.display = 'none';
                }
            });
        }

        window.openScheduleHierarchyDropdown = function (type) {
            if (type === 'facility') {
                refreshScheduleFacilityOptions();
            } else if (type === 'workgroup') {
                refreshScheduleWorkgroupOptions();
            } else if (type === 'workstation') {
                refreshScheduleWorkstationOptions();
            }
            toggleScheduleDropdown(type);
        };

        function renderSchedulerTaskActions(row) {
            if (!canManageTasks) {
                return `<div class="scheduler-cell-action"><span class="text-xs text-slate-400">${Perfectlum.escapeHtml(text.noActions)}</span></div>`;
            }

            return `
                <div class="scheduler-cell-action">
                    <button type="button"
                        data-scheduler-task-toggle="${row.id}"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700">
                        <i data-lucide="more-vertical" class="h-4 w-4"></i>
                    </button>
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

        function ensureSchedulerFloatingMenu() {
            let menu = document.getElementById('scheduler-floating-action-menu');
            if (menu) {
                return menu;
            }

            menu = document.createElement('div');
            menu.id = 'scheduler-floating-action-menu';
            menu.className = 'scheduler-floating-menu hidden';
            menu.innerHTML = `
                <button type="button" data-scheduler-task-edit-action class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    <i data-lucide="calendar-clock" class="h-4 w-4 text-sky-500"></i>
                    ${Perfectlum.escapeHtml(text.scheduleTask)}
                </button>
                <button type="button" data-scheduler-task-delete-action class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                    ${Perfectlum.escapeHtml(text.deleteTask)}
                </button>
            `;

            document.body.appendChild(menu);
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            return menu;
        }

        function closeSchedulerTaskMenus() {
            const menu = document.getElementById('scheduler-floating-action-menu');
            if (menu) {
                menu.classList.add('hidden');
                delete menu.dataset.taskId;
            }
        }

        function openSchedulerTaskMenu(trigger, taskId) {
            const menu = ensureSchedulerFloatingMenu();
            const rect = trigger.getBoundingClientRect();
            const menuWidth = 176;
            const viewportPadding = 16;
            const left = Math.min(
                Math.max(viewportPadding, rect.right - menuWidth),
                window.innerWidth - menuWidth - viewportPadding
            );
            const top = Math.min(
                rect.bottom + 10,
                window.innerHeight - 120
            );

            menu.dataset.taskId = String(taskId);
            menu.style.left = `${left}px`;
            menu.style.top = `${top}px`;
            menu.classList.remove('hidden');
        }

        function reloadSchedulerGrid() {
            schedulerTableState.autoRefreshTicks = 0;
            loadSchedulerTasks({ withoutTotal: true, silent: false })
                .then(() => queueSchedulerTotalSync(180));
            queueSchedulerAutoRefresh();
        }

        function queueSchedulerTotalSync(delay = 120) {
            if (schedulerTableState.totalSyncTimer) {
                clearTimeout(schedulerTableState.totalSyncTimer);
                schedulerTableState.totalSyncTimer = null;
            }

            schedulerTableState.totalSyncTimer = window.setTimeout(async () => {
                schedulerTableState.totalSyncTimer = null;
                if (!document.body?.contains(document.getElementById('scheduler-tasks-body'))) {
                    return;
                }
                if (schedulerTableState.fetching) {
                    queueSchedulerTotalSync(220);
                    return;
                }
                await loadSchedulerTasks({ withoutTotal: false, silent: true });
            }, delay);
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

        function renderSchedulerDateTimeCell(value, dateClass = 'text-xs text-slate-600', timeClass = 'text-[11px] font-medium text-slate-400') {
            const raw = String(value || '').trim();
            if (!raw || raw === '-' || raw === 'Not recorded') {
                return `<div class="flex flex-col items-center text-center"><span class="${Perfectlum.escapeHtml(dateClass)}">${Perfectlum.escapeHtml(raw || '-')}</span></div>`;
            }

            const normalized = raw.replace(/\s+/g, ' ').trim();
            const parts = normalized.split(' ');
            let datePart = normalized;
            let timePart = '';

            if (parts.length >= 4) {
                datePart = parts.slice(0, 3).join(' ');
                timePart = parts.slice(3).join(' ');
            } else if (parts.length === 2 && /^\d{1,2}:\d{2}(:\d{2})?$/.test(parts[1])) {
                datePart = parts[0];
                timePart = parts[1];
            }

            return `
                <div class="flex flex-col items-center text-center leading-tight">
                    <span class="${Perfectlum.escapeHtml(dateClass)}">${Perfectlum.escapeHtml(datePart)}</span>
                    ${timePart ? `<span class="${Perfectlum.escapeHtml(timeClass)}">${Perfectlum.escapeHtml(timePart)}</span>` : ''}
                </div>
            `;
        }

        function getSchedulerTaskPillClass(scheduleName) {
            const key = String(scheduleName || '').trim().toLowerCase();

            switch (key) {
                case 'start-up':
                case 'startup':
                    return 'scheduler-task-pill--startup';
                case 'once':
                    return 'scheduler-task-pill--once';
                case 'daily':
                    return 'scheduler-task-pill--daily';
                case 'weekly':
                    return 'scheduler-task-pill--weekly';
                case 'monthly':
                    return 'scheduler-task-pill--monthly';
                case 'quarterly':
                    return 'scheduler-task-pill--quarterly';
                case 'semiannually':
                case 'semi-annual':
                case 'semiannual':
                    return 'scheduler-task-pill--semiannually';
                case 'annually':
                case 'annual':
                    return 'scheduler-task-pill--annually';
                default:
                    return '';
            }
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
                const taskMetaParts = [
                    `Status: ${row.status || '-'}`
                ];
                const taskPillCls = getSchedulerTaskPillClass(row.scheduleName);

                if (row.lastExecutionAt) {
                    taskMetaParts.push(`Last Execution: ${row.lastExecutionAt}`);
                }

                const taskMeta = taskMetaParts.join(' | ');

                return `
                    <tr>
                        <td>
                            <div class="scheduler-task-cell">
                                <div class="scheduler-task-title-row">
                                    <span class="scheduler-task-title ${taskTitleCls}">${Perfectlum.escapeHtml(row.taskName || '-')}</span>
                                    <span class="scheduler-task-pill ${taskPillCls}">${Perfectlum.escapeHtml(row.scheduleName || '-')}</span>
                                </div>
                                <div class="scheduler-task-meta">${Perfectlum.escapeHtml(taskMeta)}</div>
                            </div>
                        </td>
                        <td>${renderSchedulerDisplayCell(row)}</td>
                        <td class="hidden"></td>
                        <td>${renderSchedulerDateTimeCell(row.dueAt || '-', `text-xs ${dueCls}`, 'text-[11px] font-medium text-slate-400')}</td>
                        <td>${renderSchedulerDateTimeCell(row.createdAt || 'Not recorded', `text-xs ${createdCls}`, 'text-[11px] font-medium text-slate-400')}</td>
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
                    node.textContent = schedulerTableState.sortOrder === 'asc' ? String.fromCharCode(8593) : String.fromCharCode(8595);
                } else {
                    node.textContent = String.fromCharCode(8597);
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
            if (schedulerTableState.totalSyncTimer) {
                clearTimeout(schedulerTableState.totalSyncTimer);
                schedulerTableState.totalSyncTimer = null;
            }
            if (schedulerTableState.searchTimer) {
                clearTimeout(schedulerTableState.searchTimer);
                schedulerTableState.searchTimer = null;
            }
            schedulerTableState.fetching = false;
            schedulerTableState.loading = false;
            if (schedulerTableState.fetchController) {
                schedulerTableState.fetchController.abort();
                schedulerTableState.fetchController = null;
            }
            if (window.__schedulerOutsideClickHandler) {
                document.removeEventListener('click', window.__schedulerOutsideClickHandler);
                window.__schedulerOutsideClickHandler = null;
            }
            if (window.__schedulerVisibilityHandler) {
                document.removeEventListener('visibilitychange', window.__schedulerVisibilityHandler);
                window.__schedulerVisibilityHandler = null;
            }
            if (window.__schedulerFocusHandler) {
                window.removeEventListener('focus', window.__schedulerFocusHandler);
                window.__schedulerFocusHandler = null;
            }
            if (window.__schedulerBeforeUnloadHandler) {
                window.removeEventListener('beforeunload', window.__schedulerBeforeUnloadHandler);
                window.__schedulerBeforeUnloadHandler = null;
            }
            if (window.__schedulerTaskSavedHandler) {
                window.removeEventListener('task-saved', window.__schedulerTaskSavedHandler);
                window.__schedulerTaskSavedHandler = null;
            }
            if (window.__schedulerDocumentClickHandler) {
                document.removeEventListener('click', window.__schedulerDocumentClickHandler);
                window.__schedulerDocumentClickHandler = null;
            }
            if (window.__schedulerResizeHandler) {
                window.removeEventListener('resize', window.__schedulerResizeHandler);
                window.__schedulerResizeHandler = null;
            }
            if (window.__schedulerScrollHandler) {
                document.getElementById('desktop-scroll-area')?.removeEventListener('scroll', window.__schedulerScrollHandler);
                window.__schedulerScrollHandler = null;
            }
        };

        async function loadSchedulerTasks(options = {}) {
            const withoutTotal = Boolean(options.withoutTotal);
            const silent = Boolean(options.silent);
            if (schedulerTableState.fetching) return;

            schedulerTableState.fetching = true;
            schedulerTableState.fetchController = new AbortController();
            if (!silent) {
                schedulerTableState.loading = true;
                renderSchedulerPager();
                renderSchedulerRows();
            }

            try {
                const payload = await Perfectlum.request(schedulerTasksUrl({ withoutTotal }), {
                    signal: schedulerTableState.fetchController.signal,
                });
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
                if (error?.name === 'AbortError') {
                    return;
                }
                schedulerTableState.rows = [];
                schedulerTableState.total = 0;
                const body = document.getElementById('scheduler-tasks-body');
                if (body) {
                    body.innerHTML = `<tr><td colspan="8" class="scheduler-empty text-rose-600">${Perfectlum.escapeHtml(error.message || 'Unable to load data')}</td></tr>`;
                }
            } finally {
                schedulerTableState.fetchController = null;
                schedulerTableState.fetching = false;
                schedulerTableState.loading = false;
                renderSchedulerRows();
                renderSchedulerPager();
            }
        }

        function init() {
            ensureSchedulerDesktopMenuState();
            const body = document.getElementById('scheduler-tasks-body');
            if (!body || body.dataset.schedulerInitialized === '1') {
                return;
            }
            body.dataset.schedulerInitialized = '1';

            refreshScheduleFacilityOptions();
            updateScheduleSubmitState();

            const facilitySearchInput = document.getElementById('schedule-facility-search');
            if (facilitySearchInput?.__schedulerInputHandler) {
                facilitySearchInput.removeEventListener('input', facilitySearchInput.__schedulerInputHandler);
            }
            facilitySearchInput.__schedulerInputHandler = (e) => {
                scheduleHierarchyState.facilitySearch = e.target.value || '';
                refreshScheduleFacilityOptions();
            };
            facilitySearchInput?.addEventListener('input', facilitySearchInput.__schedulerInputHandler);

            const workgroupSearchInput = document.getElementById('schedule-workgroup-search');
            if (workgroupSearchInput?.__schedulerInputHandler) {
                workgroupSearchInput.removeEventListener('input', workgroupSearchInput.__schedulerInputHandler);
            }
            workgroupSearchInput.__schedulerInputHandler = (e) => {
                scheduleHierarchyState.workgroupSearch = e.target.value || '';
                refreshScheduleWorkgroupOptions();
            };
            workgroupSearchInput?.addEventListener('input', workgroupSearchInput.__schedulerInputHandler);

            const workstationSearchInput = document.getElementById('schedule-workstation-search');
            if (workstationSearchInput?.__schedulerInputHandler) {
                workstationSearchInput.removeEventListener('input', workstationSearchInput.__schedulerInputHandler);
            }
            workstationSearchInput.__schedulerInputHandler = (e) => {
                scheduleHierarchyState.workstationSearch = e.target.value || '';
                refreshScheduleWorkstationOptions();
            };
            workstationSearchInput?.addEventListener('input', workstationSearchInput.__schedulerInputHandler);

            const displaysSearchInput = document.getElementById('schedule-displays-search');
            if (displaysSearchInput?.__schedulerInputHandler) {
                displaysSearchInput.removeEventListener('input', displaysSearchInput.__schedulerInputHandler);
            }
            displaysSearchInput.__schedulerInputHandler = filterScheduleDisplays;
            displaysSearchInput?.addEventListener('input', displaysSearchInput.__schedulerInputHandler);

            if (window.__schedulerOutsideClickHandler) {
                document.removeEventListener('click', window.__schedulerOutsideClickHandler);
            }
            window.__schedulerOutsideClickHandler = (event) => {
                if (
                    !document.getElementById('schedule-facility-panel')?.contains(event.target) &&
                    !document.getElementById('schedule-facility-trigger')?.contains(event.target) &&
                    !document.getElementById('schedule-workgroup-panel')?.contains(event.target) &&
                    !document.getElementById('schedule-workgroup-trigger')?.contains(event.target) &&
                    !document.getElementById('schedule-workstation-panel')?.contains(event.target) &&
                    !document.getElementById('schedule-workstation-trigger')?.contains(event.target) &&
                    !document.getElementById('schedule_displays_field')?.contains(event.target) &&
                    !document.getElementById('schedule_displays_dropdown')?.contains(event.target)
                ) {
                    closeScheduleDropdowns();
                }
            };
            document.addEventListener('click', window.__schedulerOutsideClickHandler);

            const searchInput = document.getElementById('scheduler-table-search');
            if (searchInput?.__schedulerSearchHandler) {
                searchInput.removeEventListener('input', searchInput.__schedulerSearchHandler);
            }
            searchInput.__schedulerSearchHandler = (event) => {
                clearTimeout(schedulerTableState.searchTimer);
                schedulerTableState.searchTimer = window.setTimeout(() => {
                    schedulerTableState.search = String(event.target.value || '').trim();
                    schedulerTableState.page = 1;
                    schedulerTableState.autoRefreshTicks = 0;
                    loadSchedulerTasks({ withoutTotal: true, silent: false })
                        .then(() => queueSchedulerTotalSync(180));
                    queueSchedulerAutoRefresh();
                }, 350);
            };
            searchInput?.addEventListener('input', searchInput.__schedulerSearchHandler);

            document.querySelectorAll('[data-scheduler-sort]').forEach((button) => {
                if (button.__schedulerSortHandler) {
                    button.removeEventListener('click', button.__schedulerSortHandler);
                }
                button.__schedulerSortHandler = (event) => {
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
                    loadSchedulerTasks({ withoutTotal: true, silent: false })
                        .then(() => queueSchedulerTotalSync(180));
                    queueSchedulerAutoRefresh();
                };
                button.addEventListener('click', button.__schedulerSortHandler);
            });

            const prevButton = document.getElementById('scheduler-page-prev');
            if (prevButton?.__schedulerPageHandler) {
                prevButton.removeEventListener('click', prevButton.__schedulerPageHandler);
            }
            prevButton.__schedulerPageHandler = () => {
                if (schedulerTableState.page <= 1 || schedulerTableState.loading || schedulerTableState.fetching) return;
                schedulerTableState.page -= 1;
                schedulerTableState.autoRefreshTicks = 0;
                loadSchedulerTasks({ withoutTotal: false, silent: false });
                queueSchedulerAutoRefresh();
            };
            prevButton?.addEventListener('click', prevButton.__schedulerPageHandler);

            const nextButton = document.getElementById('scheduler-page-next');
            if (nextButton?.__schedulerPageHandler) {
                nextButton.removeEventListener('click', nextButton.__schedulerPageHandler);
            }
            nextButton.__schedulerPageHandler = () => {
                const totalPages = Math.max(1, Math.ceil(schedulerTableState.total / schedulerTableState.limit));
                if (schedulerTableState.page >= totalPages || schedulerTableState.loading || schedulerTableState.fetching) return;
                schedulerTableState.page += 1;
                schedulerTableState.autoRefreshTicks = 0;
                loadSchedulerTasks({ withoutTotal: false, silent: false });
                queueSchedulerAutoRefresh();
            };
            nextButton?.addEventListener('click', nextButton.__schedulerPageHandler);

            updateSchedulerSortIndicators();
            loadSchedulerTasks({ withoutTotal: true, silent: false })
                .then(() => queueSchedulerTotalSync(180));
            queueSchedulerAutoRefresh();
            if (window.__schedulerVisibilityHandler) {
                document.removeEventListener('visibilitychange', window.__schedulerVisibilityHandler);
            }
            window.__schedulerVisibilityHandler = queueSchedulerAutoRefresh;
            document.addEventListener('visibilitychange', window.__schedulerVisibilityHandler);

            if (window.__schedulerFocusHandler) {
                window.removeEventListener('focus', window.__schedulerFocusHandler);
            }
            window.__schedulerFocusHandler = queueSchedulerAutoRefresh;
            window.addEventListener('focus', window.__schedulerFocusHandler);

            if (window.__schedulerBeforeUnloadHandler) {
                window.removeEventListener('beforeunload', window.__schedulerBeforeUnloadHandler);
            }
            window.__schedulerBeforeUnloadHandler = () => {
                if (schedulerTableState.autoRefreshTimer) {
                    clearTimeout(schedulerTableState.autoRefreshTimer);
                    schedulerTableState.autoRefreshTimer = null;
                }
            };
            window.addEventListener('beforeunload', window.__schedulerBeforeUnloadHandler);
        }

        if (window.__schedulerDocumentClickHandler) {
            document.removeEventListener('click', window.__schedulerDocumentClickHandler);
        }
        window.__schedulerDocumentClickHandler = (event) => {
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
                const menu = document.getElementById('scheduler-floating-action-menu');
                const willOpen = !menu || menu.classList.contains('hidden') || menu.dataset.taskId !== String(id);
                closeSchedulerTaskMenus();
                if (willOpen) {
                    openSchedulerTaskMenu(toggle, id);
                }
                return;
            }

            const editButton = event.target.closest('[data-scheduler-task-edit-action]');
            if (editButton) {
                event.preventDefault();
                event.stopPropagation();
                const taskId = document.getElementById('scheduler-floating-action-menu')?.dataset.taskId;
                closeSchedulerTaskMenus();
                if (taskId) {
                    window.edit_task(null, taskId);
                }
                return;
            }

            const deleteButton = event.target.closest('[data-scheduler-task-delete-action]');
            if (deleteButton) {
                event.preventDefault();
                event.stopPropagation();
                const taskId = document.getElementById('scheduler-floating-action-menu')?.dataset.taskId;
                closeSchedulerTaskMenus();
                if (taskId) {
                    window.openTaskDeleteConfirm?.({
                        onConfirm: () => deleteSchedulerTask(taskId)
                    });
                }
                return;
            }

            if (!event.target.closest('#scheduler-floating-action-menu')) {
                closeSchedulerTaskMenus();
            }
        };
        document.addEventListener('click', window.__schedulerDocumentClickHandler);

        if (window.__schedulerResizeHandler) {
            window.removeEventListener('resize', window.__schedulerResizeHandler);
        }
        window.__schedulerResizeHandler = closeSchedulerTaskMenus;
        window.addEventListener('resize', window.__schedulerResizeHandler);

        if (window.__schedulerScrollHandler) {
            document.getElementById('desktop-scroll-area')?.removeEventListener('scroll', window.__schedulerScrollHandler);
        }
        window.__schedulerScrollHandler = closeSchedulerTaskMenus;
        document.getElementById('desktop-scroll-area')?.addEventListener('scroll', window.__schedulerScrollHandler, { passive: true });

        if (window.__schedulerTaskSavedHandler) {
            window.removeEventListener('task-saved', window.__schedulerTaskSavedHandler);
        }
        window.__schedulerTaskSavedHandler = reloadSchedulerGrid;
        window.addEventListener('task-saved', window.__schedulerTaskSavedHandler);

        let schedulerInitQueued = false;
        function queueSchedulerInit() {
            if (schedulerInitQueued) {
                return;
            }

            schedulerInitQueued = true;
            const raf = window.requestAnimationFrame || ((callback) => window.setTimeout(callback, 16));
            raf(() => {
                window.setTimeout(() => {
                    schedulerInitQueued = false;
                    init();
                }, 0);
            });
        }

        window.schedulerPageMount = function () {
            ensureSchedulerDesktopMenuState();
            queueSchedulerInit();
            window.dispatchEvent(new Event('scheduler-page-mounted'));
        };

        document.readyState === 'loading'
            ? document.addEventListener('DOMContentLoaded', queueSchedulerInit)
            : queueSchedulerInit();
    })();
</script>


@include('tasks.schedule_task_modal')
@include('tasks.delete_task_confirm_modal')
@include('common.navigations.footer')
