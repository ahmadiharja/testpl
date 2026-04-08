@include('common.navigations.header')

@php
    $dashboardRole = session('role');
    $isSuperDashboard = $dashboardRole === 'super';
    $isAdminDashboard = $dashboardRole === 'admin';
    $isUserDashboard = $dashboardRole === 'user';

    $dashboardDescription = $isSuperDashboard
        ? __('Monitor cross-facility display health, fleet activity, and upcoming tasks from one operational surface.')
        : ($isAdminDashboard
            ? __('Track display health, recent activity, and schedule pressure inside your assigned facility.')
            : __('Review display health, recent activity, and upcoming tasks in a read-only workspace.'));

    $dashboardScopePill = $isSuperDashboard
        ? ['label' => __('Super scope'), 'tone' => 'border-sky-200 bg-sky-50 text-sky-700']
        : ($isAdminDashboard
            ? ['label' => __('Facility scope'), 'tone' => 'border-emerald-200 bg-emerald-50 text-emerald-700']
            : ['label' => __('Read only'), 'tone' => 'border-slate-200 bg-slate-50 text-slate-600']);

    $dueTasksFullUrl = $isUserDashboard ? url('due-tasks') : url('scheduler');

    $failedSectionDescription = $isUserDashboard
        ? __('The latest ten displays with active issues inside your visible scope.')
        : __('The most recent ten failed displays across the visible scope.');

    $recentSectionDescription = $isUserDashboard
        ? __('The latest ten completed test results available to your account.')
        : __('The latest ten completed test results in the current scope.');

    $dueSectionDescription = $isUserDashboard
        ? __('The next ten due tasks in a read-only schedule overview.')
        : __('The next ten due tasks across calibration and QA schedules.');

    $dashboardText = [
        'healthy' => __('Healthy'),
        'displaysOk' => __('Displays OK'),
        'displaysNotOk' => __('Displays Not OK'),
        'dueTasks' => __('Due Tasks'),
        'displaysNeedAttention' => __('Displays that need attention'),
        'openFullPage' => __('Open Full Page'),
        'noActions' => __('No actions'),
        'healthyDisplays' => __('Healthy displays'),
        'displaysNeedFollowUp' => __('Displays that need follow-up'),
        'upcomingDueTasks' => __('Upcoming due tasks'),
    ];

    $dashboardJs = [
        'historiesUrl' => url('histories-reports'),
        'canManageDashboardTasks' => in_array(session('role'), ['super', 'admin'], true),
        'noActions' => __('No actions'),
        'scheduleTask' => __('Schedule Task'),
        'deleteTask' => __('Delete Task'),
        'csrfToken' => csrf_token(),
        'deleteTaskUrl' => url('delete-task'),
        'modals' => [
            'displays_ok' => [
                'eyebrow' => __('Displays OK'),
                'title' => $dashboardText['healthyDisplays'],
                'description' => __('Review the latest ten displays that are currently reported as healthy.'),
                'fullUrl' => url('displays?type=ok'),
            ],
            'displays_failed' => [
                'eyebrow' => __('Displays Not OK'),
                'title' => $dashboardText['displaysNeedFollowUp'],
                'description' => __('Review the latest ten failed displays and jump straight into the affected hierarchy.'),
                'fullUrl' => url('displays?type=failed'),
            ],
            'workstations' => [
                'eyebrow' => __('Workstations'),
                'title' => __('Workstation fleet overview'),
                'description' => __('Scan workstation identity, hierarchy scope, connected state, and display coverage from one compact view.'),
                'fullUrl' => url('workstations'),
            ],
            'due_tasks' => [
                'eyebrow' => __('Due Tasks'),
                'title' => $dashboardText['upcomingDueTasks'],
                'description' => __('Review the next ten scheduled items with their target display, hierarchy scope, cadence, due date, and current status.'),
                'fullUrl' => $dueTasksFullUrl,
            ],
        ],
        'historySummary' => [
            'eyebrow' => __('History Summary'),
            'loadingTitle' => __('Loading report...'),
            'loadingSubtitle' => __('Preparing history summary.'),
            'loadingBody' => __('Loading history summary...'),
            'printPreview' => __('Print Preview'),
            'close' => __('Close'),
            'summaryTitle' => __('History Summary'),
            'loadingReportSummary' => __('Loading report summary...'),
            'failedToLoadHistorySummary' => __('Failed to load history summary.'),
            'detailedSummaryForTask' => __('Detailed summary for the selected task execution.'),
            'noStructuredSummary' => __('No structured summary is available for this history record.'),
            'facility' => __('Facility'),
            'workgroup' => __('Workgroup'),
            'workstation' => __('Workstation'),
            'displayLabel' => __('Display'),
            'performedAt' => __('Performed At'),
            'result' => __('Result'),
            'section' => __('Section'),
            'reviewScoredChecks' => __('Review scored checks, question answers, and comments captured for this task.'),
            'score' => __('Score'),
            'limit' => __('Limit'),
            'measured' => __('Measured'),
            'status' => __('Status'),
            'comment' => __('Comment'),
        ],
        'grid' => [
            'display' => __('Display'),
            'workstation' => __('Workstation'),
            'workgroup' => __('Workgroup'),
            'facility' => __('Facility'),
            'status' => __('Status'),
            'task' => __('Task'),
            'taskName' => __('Task Name'),
            'schedule' => __('Schedule'),
            'scheduleType' => __('Schedule Type'),
            'dueDate' => __('Due Date'),
            'actions' => __('Actions'),
            'lastUpdate' => __('Latest activity'),
            'errorDetails' => __('Attention'),
            'result' => __('Result'),
            'performed' => __('Performed'),
            'ok' => __('OK'),
            'fail' => __('Fail'),
            'pass' => __('Pass'),
            'overdue' => __('Overdue'),
            'today' => __('Today'),
            'upcoming' => __('Upcoming'),
            'inService' => __('in service'),
            'requireFollowUp' => __('require follow-up'),
            'fleet' => __('Fleet'),
            'activeRecords' => __('active records'),
            'pipeline' => __('Pipeline'),
            'scheduledItems' => __('scheduled items'),
            'displayNotOk' => __('Display Not OK'),
            'viewAll' => __('View All'),
            'noFailedDisplaysDetected' => __('No failed displays detected'),
            'failedDisplaysEmptyDescription' => __('This area will list displays with recent errors as soon as any issue is reported.'),
            'latestPerformed' => __('Latest Performed'),
            'recentActivity' => __('Recent activity'),
            'openScheduler' => __('Open Scheduler'),
            'upcomingMaintenancePipeline' => __('Upcoming maintenance pipeline'),
            'facilityAdminScope' => __('Facility Admin Scope'),
            'managingOneFacilityWorkspace' => __('You are managing one facility workspace'),
            'bulkActionsScopedPrefix' => __('Bulk actions, task scheduling, and workstation changes remain scoped to'),
            'yourAssignedFacility' => __('your assigned facility'),
            'facility' => __('Facility'),
            'assignedFacility' => __('Assigned facility'),
            'recordsToReview' => __('records to review'),
            'recentDueTasks' => __('Recent Due Tasks'),
            'upcomingItems' => __('upcoming items'),
            'readOnlyWorkspace' => __('Read-only Workspace'),
            'dashboardDesignedForMonitoring' => __('This dashboard is designed for monitoring'),
            'readOnlyScopeDescription' => __('You can review workstation health, schedule pressure, and recent activity, but task management and configuration actions stay hidden.'),
            'remotePortalInfo' => __('Remote Portal Info'),
            'clientConnectionDetails' => __('Client connection details'),
            'remotePortalDescription' => __('Use these values to connect a remote client to this environment without leaving the dashboard.'),
            'downloadClient' => __('Download Client'),
            'endpointUrl' => __('Endpoint URL'),
            'serviceId' => __('Service ID'),
            'tokenPk' => __('Token PK'),
        ],
    ];
@endphp

<div class="mx-auto flex w-full max-w-[1600px] flex-col gap-6 pb-10">
    <x-page-header
        title="{{ __('Dashboard') }}"
        :description="$dashboardDescription"
        icon="layout-dashboard"
    >
        <x-slot name="actions">
            <span class="inline-flex items-center rounded-full border px-3 py-2 text-xs font-bold uppercase tracking-[0.16em] {{ $dashboardScopePill['tone'] }}">
                {{ $dashboardScopePill['label'] }}
            </span>
            <button
                type="button"
                onclick="window.refreshDashboardGrids && window.refreshDashboardGrids()"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                {{ __('Refresh') }}
            </button>
        </x-slot>
    </x-page-header>

    <section class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <button
            type="button"
            onclick="window.openDashboardStatModal && window.openDashboardStatModal('displays_ok')"
            class="rounded-[2rem] border border-slate-200/80 bg-white p-6 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
        >
            <div class="flex items-start justify-between gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 shadow-sm">
                    <i data-lucide="monitor-check" class="h-6 w-6"></i>
                </div>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-emerald-700">
                    {{ $dashboardText['healthy'] }}
                </span>
            </div>
            <div class="mt-6">
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ $dashboardText['displaysOk'] }}</p>
                <div class="mt-2 flex items-end gap-3">
                    <p class="text-5xl font-black tracking-tight text-slate-900">{{ $d_ok }}</p>
                    <p class="pb-1 text-sm font-semibold text-emerald-600">{{ __('in service') }}</p>
                </div>
            </div>
        </button>

        <button
            type="button"
            onclick="window.openDashboardStatModal && window.openDashboardStatModal('displays_failed')"
            class="rounded-[2rem] border border-rose-200 bg-white p-6 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
        >
            <div class="flex items-start justify-between gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 shadow-sm">
                    <i data-lucide="alert-octagon" class="h-6 w-6"></i>
                </div>
                <span class="rounded-full bg-rose-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-rose-700">
                    {{ __('Attention') }}
                </span>
            </div>
            <div class="mt-6">
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ $dashboardText['displaysNotOk'] }}</p>
                <div class="mt-2 flex items-end gap-3">
                    <p class="text-5xl font-black tracking-tight text-slate-900">{{ $d_fail }}</p>
                    <p class="pb-1 text-sm font-semibold text-rose-600">{{ __('require follow-up') }}</p>
                </div>
            </div>
        </button>

        <button
            type="button"
            onclick="window.openDashboardStatModal && window.openDashboardStatModal('workstations')"
            class="rounded-[2rem] border border-slate-200/80 bg-white p-6 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
        >
            <div class="flex items-start justify-between gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 shadow-sm">
                    <i data-lucide="layout-grid" class="h-6 w-6"></i>
                </div>
                <span class="rounded-full bg-sky-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-sky-700">
                    {{ __('Fleet') }}
                </span>
            </div>
            <div class="mt-6">
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ __('Workstations') }}</p>
                <div class="mt-2 flex items-end gap-3">
                    <p class="text-5xl font-black tracking-tight text-slate-900">{{ $workstations }}</p>
                    <p class="pb-1 text-sm font-semibold text-sky-600">{{ __('active records') }}</p>
                </div>
            </div>
        </button>

        <button
            type="button"
            onclick="window.openDashboardStatModal && window.openDashboardStatModal('due_tasks')"
            class="rounded-[2rem] border border-amber-200 bg-white p-6 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
        >
            <div class="flex items-start justify-between gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 shadow-sm">
                    <i data-lucide="calendar-clock" class="h-6 w-6"></i>
                </div>
                <span class="rounded-full bg-amber-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-amber-700">
                    {{ __('Pipeline') }}
                </span>
            </div>
            <div class="mt-6">
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ $dashboardText['dueTasks'] }}</p>
                <div class="mt-2 flex items-end gap-3">
                    <p id="due_tasks_stats" class="text-5xl font-black tracking-tight text-slate-900">{{ $due_tasks }}</p>
                    <p class="pb-1 text-sm font-semibold text-amber-600">{{ __('scheduled items') }}</p>
                </div>
            </div>
        </button>
    </section>

    <section class="dashboard-top-dual">
        <div class="dashboard-calibrate-card min-w-0 p-5">
            <div class="mb-4 flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ __('Display Not OK') }}</p>
                    <h3 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ $dashboardText['displaysNeedAttention'] }}</h3>
                    <p class="mt-1 text-[13px] text-slate-500">{{ $failedSectionDescription }}</p>
                </div>
                <button
                    type="button"
                    onclick="window.openDashboardStatModal && window.openDashboardStatModal('displays_failed')"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold uppercase tracking-[0.16em] text-slate-500 transition hover:bg-slate-50"
                >
                    {{ __('View All') }}
                    <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                </button>
            </div>

            <div class="dashboard-calibrate-table-shell dashboard-scroll-y dashboard-scroll-failed overflow-hidden">
                @if($d_fail == 0)
                    <div class="flex min-h-[20rem] flex-col items-center justify-center px-6 py-12 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-[1.5rem] bg-emerald-50 text-emerald-600 shadow-sm">
                            <i data-lucide="shield-check" class="h-8 w-8"></i>
                        </div>
                        <h4 class="mt-5 text-lg font-bold text-slate-900">{{ __('No failed displays detected') }}</h4>
                        <p class="mt-2 max-w-md text-sm text-slate-500">{{ __('This area will list displays with recent errors as soon as any issue is reported.') }}</p>
                    </div>
                @else
                    <div id="failed-displays-grid" class="w-full"></div>
                @endif
            </div>
        </div>

        <div class="min-w-0">
            <div class="dashboard-calibrate-card min-w-0 p-5">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ __('Latest Performed') }}</p>
                        <h3 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ __('Recent activity') }}</h3>
                        <p class="mt-1 text-[13px] text-slate-500">{{ $recentSectionDescription }}</p>
                    </div>
                    <button type="button" onclick="window.refreshDashboardGrids && window.refreshDashboardGrids()" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-50">
                        <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                    </button>
                </div>

                <div class="dashboard-calibrate-table-shell dashboard-scroll-y dashboard-scroll-latest overflow-hidden">
                    <div id="latest-performed-grid" class="w-full"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-calibrate-card p-5">
        <div class="mb-4 flex items-start justify-between gap-4">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ __('Due Tasks') }}</p>
                <h3 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ __('Upcoming maintenance pipeline') }}</h3>
                <p class="mt-1 text-[13px] text-slate-500">{{ $dueSectionDescription }}</p>
            </div>
            <button
                type="button"
                onclick="window.openDashboardStatModal && window.openDashboardStatModal('due_tasks')"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold uppercase tracking-[0.16em] text-slate-500 transition hover:bg-slate-50"
            >
                {{ __('View All') }}
                <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
            </button>
        </div>

        <div class="dashboard-calibrate-table-shell dashboard-scroll-y dashboard-scroll-due overflow-hidden">
            <div id="due-tasks-grid" class="w-full"></div>
        </div>
    </section>

    @unless($isUserDashboard)
    <section class="dashboard-calibrate-card px-5 py-4">
        <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ __('Remote Portal Info') }}</p>
                <h3 class="mt-1 text-lg font-bold tracking-tight text-slate-900">{{ __('Client connection details') }}</h3>
            </div>

            <div class="flex min-w-0 flex-1 flex-col gap-2 xl:flex-row xl:items-center xl:justify-end">
                <div class="flex min-w-0 items-center gap-2 rounded-xl border border-slate-200 bg-white/85 px-3 py-2">
                    <span class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">{{ __('Endpoint URL') }}</span>
                    <span class="min-w-0 truncate text-xs font-semibold text-slate-700">{{ url('/') }}</span>
                    <button type="button" onclick="copy_field('#endpoint_url')" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100">
                        <i data-lucide="copy" class="h-3.5 w-3.5"></i>
                    </button>
                    <input id="endpoint_url" type="hidden" value="{{ url('/') }}">
                </div>

                <div class="flex min-w-0 items-center gap-2 rounded-xl border border-slate-200 bg-white/85 px-3 py-2">
                    <span class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">{{ __('Service ID') }}</span>
                    <span class="min-w-0 truncate text-xs font-semibold text-slate-700">{{ $user->sync_user }}</span>
                    <button type="button" onclick="copy_field('#sync_user')" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100">
                        <i data-lucide="copy" class="h-3.5 w-3.5"></i>
                    </button>
                    <input id="sync_user" type="hidden" value="{{ $user->sync_user }}">
                </div>

                <div class="flex min-w-0 items-center gap-2 rounded-xl border border-slate-200 bg-white/85 px-3 py-2">
                    <span class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">{{ __('Token PK') }}</span>
                    <span class="min-w-0 truncate text-xs font-semibold text-slate-700">{{ $user->sync_password_raw }}</span>
                    <button type="button" onclick="copy_field('#sync_pass')" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100">
                        <i data-lucide="copy" class="h-3.5 w-3.5"></i>
                    </button>
                    <input id="sync_pass" type="hidden" value="{{ $user->sync_password_raw }}">
                </div>

                <a
                    href="https://qubyx.com/product/remote-server/"
                    target="_blank"
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 text-xs font-bold uppercase tracking-[0.12em] text-white transition hover:bg-sky-600"
                >
                    <i data-lucide="download-cloud" class="h-3.5 w-3.5"></i>
                    {{ __('Download Client') }}
                </a>
            </div>
        </div>
    </section>
    @endunless

    @if($isAdminDashboard)
    <section class="rounded-[2rem] border border-emerald-200/80 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ __('Facility Admin Scope') }}</p>
                <h3 class="mt-1 text-xl font-bold tracking-tight text-slate-900">{{ __('You are managing one facility workspace') }}</h3>
                <p class="mt-1 text-[13px] text-slate-500">{{ __('Bulk actions, task scheduling, and workstation changes remain scoped to') }} {{ optional($user->facility)->name ?? __('your assigned facility') }}.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Facility') }}</p>
                    <p class="mt-2 text-sm font-bold text-slate-900">{{ optional($user->facility)->name ?? __('Assigned facility') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Workstations') }}</p>
                    <p class="mt-2 text-sm font-bold text-slate-900">{{ $workstations }} {{ __('active records') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Due Tasks') }}</p>
                    <p class="mt-2 text-sm font-bold text-slate-900">{{ $due_tasks }} {{ __('scheduled items') }}</p>
                </div>
            </div>
        </div>
    </section>
    @elseif($isUserDashboard)
    <section class="rounded-[2rem] border border-slate-200/80 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ __('Read-only Workspace') }}</p>
                <h3 class="mt-1 text-xl font-bold tracking-tight text-slate-900">{{ __('This dashboard is designed for monitoring') }}</h3>
                <p class="mt-1 text-[13px] text-slate-500">{{ __('You can review workstation health, schedule pressure, and recent activity, but task management and configuration actions stay hidden.') }}</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Assigned Facility') }}</p>
                    <p class="mt-2 text-sm font-bold text-slate-900">{{ optional($user->facility)->name ?? __('Assigned facility') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Displays Not OK') }}</p>
                    <p class="mt-2 text-sm font-bold text-slate-900">{{ $d_fail }} {{ __('records to review') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Recent Due Tasks') }}</p>
                    <p class="mt-2 text-sm font-bold text-slate-900">{{ $due_tasks }} {{ __('upcoming items') }}</p>
                </div>
            </div>
        </div>
    </section>
    @endif

</div>

@include('tasks.schedule_task_modal')
@include('tasks.delete_task_confirm_modal')

<style>
    .dashboard-calibrate-card {
        border-radius: 2rem;
        border: 1px solid rgb(186 230 253 / 0.6);
        background: linear-gradient(180deg, rgba(248, 252, 255, 0.96), rgba(240, 249, 255, 0.76));
        box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
    }

    .dashboard-calibrate-table-shell {
        border-radius: 1.5rem;
        border: 1px solid rgb(191 219 254 / 0.65);
        background: rgba(255, 255, 255, 0.72);
        padding: 0;
        overflow: hidden;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.95);
    }

    .dashboard-top-dual {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 1.5rem;
    }

    @media (min-width: 1280px) {
        .dashboard-top-dual {
            grid-template-columns: minmax(0, 1.32fr) minmax(0, 1fr);
        }
    }

    .dashboard-editorial-scroll {
        overflow: auto;
    }

    .dashboard-editorial-scroll::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .dashboard-editorial-scroll::-webkit-scrollbar-thumb {
        background: rgb(203 213 225 / 0.95);
        border-radius: 999px;
    }

    .dashboard-editorial-scroll::-webkit-scrollbar-track {
        background: rgb(248 250 252 / 0.95);
    }

    .dashboard-editorial-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .dashboard-editorial-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8fbff;
        border-bottom: 1px solid rgb(226 232 240 / 0.9);
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: rgb(71 85 105);
        white-space: nowrap;
    }

    .dashboard-editorial-table thead th.text-center {
        text-align: center;
    }

    .dashboard-editorial-table tbody td {
        border-bottom: 1px solid rgb(226 232 240 / 0.75);
        padding: 0.7rem 1rem;
        vertical-align: middle;
        background: transparent;
    }

    .dashboard-editorial-table tbody tr:hover td {
        background: #f8fbff;
    }

    .dashboard-task-modal-open .dashboard-editorial-table thead th {
        position: static !important;
    }
</style>

<div
    id="dashboard-stat-modal"
    class="pointer-events-none fixed inset-0 z-[90] hidden opacity-0 transition duration-200"
    aria-hidden="true"
>
    <div id="dashboard-stat-modal-overlay" class="absolute inset-0 bg-slate-950/45 backdrop-blur-[2px]"></div>
    <div class="flex min-h-full items-center justify-center px-4 py-8">
        <div
            id="dashboard-stat-modal-panel"
            class="relative flex max-h-[88vh] w-full max-w-7xl translate-y-3 scale-[0.985] flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl transition duration-200"
        >
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
                <div class="min-w-0">
                    <p id="dashboard-stat-modal-eyebrow" class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ __('Dashboard Detail') }}</p>
                    <h3 id="dashboard-stat-modal-title" class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ __('Stat Detail') }}</h3>
                    <p id="dashboard-stat-modal-description" class="mt-1 text-[13px] text-slate-500">{{ __('Review the latest records related to this dashboard metric.') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a
                        id="dashboard-stat-modal-link"
                        href="#"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold uppercase tracking-[0.16em] text-slate-500 transition hover:bg-slate-50"
                    >
                        {{ __('Open Full Page') }}
                        <i data-lucide="arrow-up-right" class="h-3.5 w-3.5"></i>
                    </a>
                    <button
                        type="button"
                        id="dashboard-stat-modal-close"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 text-slate-500 transition hover:bg-slate-50"
                    >
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto px-6 py-4">
                <div class="workstation-table-shell overflow-hidden">
                    <div id="dashboard-stat-modal-grid" class="w-full"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="dashboard-history-summary-modal" class="fixed inset-0 z-[120] hidden">
    <div data-dashboard-history-summary-overlay class="absolute inset-0 bg-slate-950/45 backdrop-blur-[2px] opacity-0 transition-opacity duration-200"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 md:p-6">
        <div data-dashboard-history-summary-panel class="relative flex max-h-[88vh] w-full max-w-5xl translate-y-4 scale-[0.985] flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_32px_90px_rgba(15,23,42,0.24)] opacity-0 transition-all duration-200">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-sky-500">{{ $dashboardJs['historySummary']['eyebrow'] }}</p>
                    <h2 id="dashboard-history-summary-title" class="mt-1 truncate text-2xl font-semibold text-slate-900">{{ $dashboardJs['historySummary']['loadingTitle'] }}</h2>
                    <p id="dashboard-history-summary-subtitle" class="mt-2 text-sm text-slate-500">{{ $dashboardJs['historySummary']['loadingSubtitle'] }}</p>
                </div>
                <button type="button" data-dashboard-history-summary-close class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 text-slate-400 transition hover:border-slate-300 hover:text-slate-700">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <div id="dashboard-history-summary-body" class="flex-1 overflow-y-auto px-6 py-5">
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                    {{ $dashboardJs['historySummary']['loadingBody'] }}
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <a id="dashboard-history-summary-print" href="#" target="_blank" rel="noopener" class="inline-flex h-11 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                    <i data-lucide="printer" class="h-4 w-4"></i>
                    {{ $dashboardJs['historySummary']['printPreview'] }}
                </a>
                <button type="button" data-dashboard-history-summary-close class="inline-flex h-11 items-center rounded-2xl bg-sky-500 px-5 text-sm font-semibold text-white transition hover:bg-sky-600">
                    {{ $dashboardJs['historySummary']['close'] }}
                </button>
            </div>
        </div>
    </div>
</div>

<div id="dashboard-investigate-drawer" class="fixed inset-0 z-[130] hidden">
    <div data-dashboard-investigate-overlay class="absolute inset-0 bg-slate-950/45 backdrop-blur-[2px] opacity-0 transition-opacity duration-200"></div>
    <aside data-dashboard-investigate-panel class="absolute inset-y-0 right-0 flex h-full w-full max-w-2xl translate-x-full flex-col border-l border-slate-200 bg-white shadow-[0_28px_80px_rgba(15,23,42,0.28)] transition-transform duration-200">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">Investigation</p>
                <h3 id="dashboard-investigate-title" class="mt-1 truncate text-xl font-bold tracking-tight text-slate-900">Display Investigation</h3>
                <p id="dashboard-investigate-subtitle" class="mt-1 text-sm text-slate-500">Operational summary for this display.</p>
            </div>
            <button type="button" data-dashboard-investigate-close class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 text-slate-500 transition hover:bg-slate-50">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <div id="dashboard-investigate-body" class="flex-1 overflow-y-auto px-6 py-5">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">Loading investigation data...</div>
        </div>
    </aside>
</div>

<div id="dashboard-task-detail-modal" class="fixed inset-0 z-[9400] hidden">
    <div data-dashboard-task-overlay class="absolute inset-0 bg-slate-900/45 backdrop-blur-sm opacity-0 transition-opacity duration-200"></div>
    <div class="pointer-events-none absolute inset-0 flex items-center justify-center p-4 sm:p-6">
        <div data-dashboard-task-panel class="pointer-events-auto relative w-full max-w-4xl overflow-hidden rounded-[1.75rem] border border-slate-200 bg-[#F8FAFC] shadow-[0_24px_80px_rgba(15,23,42,0.24)] opacity-0 transition-all duration-200 scale-[0.985] translate-y-4">
            <div class="relative overflow-hidden bg-gradient-to-r from-[#1175FF] to-[#0A62F0] px-8 py-7 text-white">
                <div class="absolute inset-0 opacity-[0.18]" style="background-image: radial-gradient(rgba(255,255,255,1) 1.4px, transparent 1.4px); background-size: 16px 16px;"></div>
                <button type="button" data-dashboard-task-close class="absolute right-6 top-6 z-20 inline-flex h-10 w-10 items-center justify-center rounded-full bg-black/10 text-white transition hover:bg-black/20">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
                <div class="relative z-10 flex items-start gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm">
                        <i data-lucide="calendar-clock" class="h-6 w-6"></i>
                    </div>
                    <div class="space-y-2">
                        <p class="text-[11px] font-black uppercase tracking-[0.28em] text-white/80">Task Detail</p>
                        <h3 id="dashboard-task-title" class="text-3xl font-extrabold tracking-tight">Loading...</h3>
                        <p id="dashboard-task-subtitle" class="max-w-2xl text-sm text-white/80"></p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 p-8 lg:grid-cols-[1.3fr_0.9fr]">
                <div class="space-y-6">
                    <div class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Task Summary</p>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Task</p>
                                <p id="dashboard-task-name" class="mt-2 text-base font-bold text-slate-900"></p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Schedule</p>
                                <p id="dashboard-task-schedule" class="mt-2 text-base font-bold text-slate-900"></p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Due</p>
                                <p id="dashboard-task-due" class="mt-2 text-base font-bold text-slate-900"></p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Status</p>
                                <p id="dashboard-task-status" class="mt-2"></p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Display</p>
                        <button type="button" id="dashboard-task-display-link" class="mt-3 w-full rounded-[1.25rem] border border-sky-200 bg-sky-50 px-4 py-4 text-left transition hover:border-sky-300 hover:bg-sky-100/70">
                            <p id="dashboard-task-display" class="text-sm font-bold text-slate-900"></p>
                        </button>
                    </div>
                </div>

                <div class="space-y-4 rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Scope</p>
                    <div class="space-y-3 text-sm">
                        <button type="button" id="dashboard-task-facility-link" class="flex w-full items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-left transition hover:bg-slate-100">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-sky-500 text-[8px] font-black text-white">F</span>
                            <span id="dashboard-task-facility" class="truncate font-semibold text-slate-700"></span>
                        </button>
                        <button type="button" id="dashboard-task-workgroup-link" class="flex w-full items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-left transition hover:bg-slate-100">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-sky-500 text-[8px] font-black text-white">WG</span>
                            <span id="dashboard-task-workgroup" class="truncate font-semibold text-slate-700"></span>
                        </button>
                        <button type="button" id="dashboard-task-workstation-link" class="flex w-full items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-left transition hover:bg-slate-100">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-sky-500 text-[8px] font-black text-white">WS</span>
                            <span id="dashboard-task-workstation" class="truncate font-semibold text-slate-700"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
    function dashboardHistoryUrl(displayId, historyId = null) {
        if (historyId) {
            return `/histories/${historyId}`;
        }

        const url = new URL(@json($dashboardJs['historiesUrl']), window.location.origin);
        if (displayId) {
            url.searchParams.set('display_id', displayId);
        }
        return `${url.pathname}${url.search}`;
    }

    function dashboardEscapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function dashboardDisplayErrorText(errors) {
        if (Array.isArray(errors) && errors.length) {
            const latest = errors[errors.length - 1];
            if (latest && typeof latest === 'object') {
                return latest.message || @json(__('Display requires attention'));
            }

            return String(latest ?? @json(__('Display requires attention')));
        }

        if (typeof errors === 'string' && errors.trim() !== '') {
            return errors;
        }

        return @json(__('Display requires attention'));
    }

    function dashboardHierarchyButton(type, id, label, icon) {
        return `
            <button
                type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: '${type}', id: ${id} }}))"
                class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 transition hover:text-sky-600"
            >
                ${icon}
                ${dashboardEscapeHtml(label)}
            </button>
        `;
    }

    function dashboardScopeBadgeButton(type, id, label, shortCode) {
        return `
            <button
                type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: '${type}', id: ${id} }}))"
                class="inline-flex items-center gap-2 text-[11px] font-semibold text-slate-600 transition hover:text-sky-600"
            >
                <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-sky-500 text-[8px] font-black leading-none tracking-tight text-white">${dashboardEscapeHtml(shortCode)}</span>
                <span class="truncate">${dashboardEscapeHtml(label)}</span>
            </button>
        `;
    }

    function dashboardScopeInlineButton(type, id, label, shortCode) {
        return `
            <button
                type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: '${type}', id: ${id} }}))"
                class="inline-flex w-full min-w-0 items-center gap-2 text-[11px] font-semibold text-slate-600 transition hover:text-sky-600"
                title="${dashboardEscapeHtml(label)}"
            >
                <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-sky-500 text-[8px] font-black leading-none tracking-tight text-white">${dashboardEscapeHtml(shortCode)}</span>
                <span class="min-w-0 flex-1 truncate text-left">${dashboardEscapeHtml(label)}</span>
            </button>
        `;
    }

    function dashboardScopeMiniButton(type, id, label, shortCode) {
        return `
            <button
                type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: '${type}', id: ${id} }}))"
                class="inline-flex max-w-[10rem] min-w-0 items-center gap-1.5 text-[11px] font-semibold text-slate-500 transition hover:text-sky-600"
                title="${dashboardEscapeHtml(label)}"
            >
                <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-sky-500 text-[8px] font-black leading-none tracking-tight text-white">${dashboardEscapeHtml(shortCode)}</span>
                <span class="min-w-0 truncate text-left">${dashboardEscapeHtml(label)}</span>
            </button>
        `;
    }

    function dashboardRenderInvestigateAction(item) {
        return `
            <button
                type="button"
                data-dashboard-investigate="${Number(item.id || 0)}"
                data-dashboard-investigate-name="${dashboardEscapeHtml(item.displayName || '-')}"
                class="inline-flex items-center rounded-full border border-slate-300 bg-white px-3 py-1.5 text-[11px] font-bold uppercase tracking-[0.12em] text-slate-700 transition hover:border-sky-300 hover:text-sky-700 hover:bg-sky-50"
            >
                Investigate
            </button>
        `;
    }

    function dashboardActionButton({ href = null, onClick = null, tone = 'blue', icon = '' }) {
        const tones = {
            blue: 'bg-sky-50 text-sky-600 hover:bg-sky-100',
            amber: 'bg-amber-50 text-amber-600 hover:bg-amber-100',
            rose: 'bg-rose-50 text-rose-600 hover:bg-rose-100',
        };

        const attrs = href ? `href="${href}"` : `href="javascript:void(0)" onclick="${onClick}"`;
        return `
            <a ${attrs} class="inline-flex h-8 w-8 items-center justify-center rounded-lg ${tones[tone]} transition">
                ${icon}
            </a>
        `;
    }

    function dashboardPaginationItems(currentPage, totalPages) {
        if (totalPages <= 1) {
            return [1];
        }

        const pages = new Set([1, totalPages, currentPage, currentPage - 1, currentPage + 1]);
        const ordered = [...pages].filter((page) => page >= 1 && page <= totalPages).sort((a, b) => a - b);
        const items = [];

        ordered.forEach((page, index) => {
            items.push(page);
            const next = ordered[index + 1];
            if (next && next - page > 1) {
                items.push('ellipsis');
            }
        });

        return items;
    }

    function dashboardWorkstationConnectionMeta(color, timestamp) {
        if (!timestamp || timestamp === '-') {
            return {
                pill: 'border-slate-200 bg-slate-50 text-slate-600',
                label: 'No sync data',
                helper: 'This workstation has not reported a recent connection timestamp.'
            };
        }

        if (color === 'danger') {
            return {
                pill: 'border-rose-200 bg-rose-50 text-rose-700',
                label: 'Needs review',
                helper: 'Connection looks stale and may require a health check.'
            };
        }

        if (color === 'warning') {
            return {
                pill: 'border-amber-200 bg-amber-50 text-amber-700',
                label: 'Aging signal',
                helper: 'Seen recently, but this workstation should be checked soon.'
            };
        }

        return {
            pill: 'border-emerald-200 bg-emerald-50 text-emerald-700',
            label: 'Connected recently',
            helper: 'The workstation has reported a recent sync heartbeat.'
        };
    }

    function renderDashboardConnectionWatchlistItem(item) {
        const connection = dashboardWorkstationConnectionMeta(item.lcColor, item.lastConnected);
        const displayLabel = item.displaysCount === 1 ? '1 display' : `${item.displaysCount} displays`;

        return `
            <article class="rounded-[1.4rem] border border-slate-200 bg-slate-50/70 px-4 py-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <button
                            type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'workstation', id: ${item.id} }}))"
                            class="block truncate text-left text-[13px] font-bold text-slate-900 transition hover:text-sky-600"
                        >
                            ${dashboardEscapeHtml(item.name)}
                        </button>
                        <div class="mt-1 flex items-center gap-2 whitespace-nowrap text-[11px] leading-5 text-slate-500">
                            ${dashboardHierarchyButton('workgroup', item.wgId, item.wgName, hierarchyIcons.workgroup)}
                            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                            ${dashboardHierarchyButton('facility', item.facId, item.facName, hierarchyIcons.facility)}
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center rounded-full border px-2.5 py-1 text-[11px] font-bold ${connection.pill}">
                            ${dashboardEscapeHtml(connection.label)}
                        </span>
                        ${dashboardActionButton({
                            onClick: `window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'workstation', id: ${item.id} } }))`,
                            tone: 'blue',
                            icon: hierarchyIcons.workstation
                        })}
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between gap-3 text-[12px]">
                    <p class="truncate text-slate-500">
                        ${dashboardEscapeHtml(item.lastSeenRelative)} ·
                        <span class="font-semibold text-slate-700">${dashboardEscapeHtml(item.lastConnected || '-')}</span>
                    </p>
                    <span class="shrink-0 rounded-full bg-white px-2.5 py-1 font-semibold text-slate-600 ring-1 ring-slate-200">${dashboardEscapeHtml(displayLabel)}</span>
                </div>
            </article>
        `;
    }

    async function renderDashboardConnectionWatchlist(limit = 5) {
        const holder = document.getElementById('connection-watchlist-widget');
        const stats = document.getElementById('connection_watchlist_stats');
        if (!holder) {
            return;
        }

        holder.innerHTML = `
            <div class="flex min-h-[12rem] items-center justify-center rounded-[1.5rem] border border-dashed border-slate-200 bg-slate-50/70 px-4 py-10 text-sm font-medium text-slate-500">
                ${@json(__('Loading connection watchlist...'))}
            </div>
        `;

        try {
            const response = await fetch(`/api/connection-watchlist?limit=${limit}&without_total=1`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const payload = await response.json();
            const items = payload.data || [];
            const total = Number(payload.total || 0);

            if (stats) {
                stats.textContent = total;
            }

            if (!items.length) {
                holder.innerHTML = `
                    <div class="flex min-h-[12rem] flex-col items-center justify-center rounded-[1.5rem] border border-dashed border-emerald-200 bg-emerald-50/60 px-5 py-10 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-emerald-600 shadow-sm">
                            <i data-lucide="wifi" class="h-6 w-6"></i>
                        </div>
                        <h4 class="mt-4 text-base font-bold text-slate-900">${@json(__('All workstations reported recently'))}</h4>
                        <p class="mt-2 max-w-sm text-sm text-slate-500">${@json(__('No stale connection records were found in the current dashboard scope.'))}</p>
                    </div>
                `;
                if (window.lucide) window.lucide.createIcons();
                return;
            }

            holder.innerHTML = `
                <div class="mb-3 flex items-center justify-between gap-3 rounded-[1.4rem] border border-slate-200 bg-slate-50/70 px-4 py-3">
                    <p class="text-[12px] font-medium text-slate-500">${@json(__('Showing the most stale workstation sync records first.'))}</p>
                    <span class="shrink-0 rounded-full bg-white px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] text-slate-600 ring-1 ring-slate-200">${total} ${@json(__('flagged'))}</span>
                </div>
                <div class="space-y-3">${items.map(renderDashboardConnectionWatchlistItem).join('')}</div>
            `;
            if (window.lucide) window.lucide.createIcons();
        } catch (error) {
            holder.innerHTML = `
                <div class="flex min-h-[12rem] items-center justify-center rounded-[1.5rem] border border-dashed border-rose-200 bg-rose-50/60 px-4 py-10 text-center text-sm font-medium text-rose-600">
                    ${@json(__('Failed to load connection watchlist.'))}
                </div>
            `;
        }
    }

    window.dashboardWorkstationModalState = window.dashboardWorkstationModalState || {
        page: 1,
        limit: 10,
        search: '',
        facilityId: '',
        workgroupId: '',
        facilities: [],
        workgroups: []
    };
    window.dashboardWorkstationSearchTimer = null;

    async function loadDashboardWorkstationFacilities() {
        const state = window.dashboardWorkstationModalState;
        if (state.facilities.length) {
            return state.facilities;
        }

        try {
            const response = await fetch('/api/facilities?page=1&limit=500', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const payload = await response.json();
            state.facilities = payload.data || [];
        } catch (error) {
            state.facilities = [];
        }

        return state.facilities;
    }

    async function loadDashboardWorkstationWorkgroups(facilityId = '') {
        const state = window.dashboardWorkstationModalState;
        const query = new URLSearchParams({ page: 1, limit: 1000 });
        if (facilityId) {
            query.set('facility_id', facilityId);
        }

        try {
            const response = await fetch(`/api/workgroups?${query.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const payload = await response.json();
            state.workgroups = payload.data || [];
        } catch (error) {
            state.workgroups = [];
        }

        return state.workgroups;
    }

    function renderDashboardWorkstationCard(item) {
        const connection = dashboardWorkstationConnectionMeta(item.lcColor, item.lastConnected);
        const displaysLabel = item.displaysCount === 1 ? '1 display attached' : `${item.displaysCount} displays attached`;

        return `
            <article class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Workstation') }}</p>
                        <button
                            type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'workstation', id: ${item.id} }}))"
                            class="mt-2 block truncate text-left text-base font-bold tracking-tight text-slate-900 transition hover:text-sky-600"
                        >
                            ${dashboardEscapeHtml(item.name)}
                        </button>
                    </div>
                    <span class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.12em] text-slate-600">
                        ${dashboardEscapeHtml(item.sleepTime || 'Off')}
                    </span>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    ${dashboardHierarchyButton('workgroup', item.wgId, item.wgName, hierarchyIcons.workgroup)}
                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                    ${dashboardHierarchyButton('facility', item.facId, item.facName, hierarchyIcons.facility)}
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Display Coverage') }}</p>
                        <div class="mt-2 flex items-end justify-between gap-3">
                            <span class="text-2xl font-black tracking-tight text-slate-900">${dashboardEscapeHtml(item.displaysCount)}</span>
                            <span class="text-[12px] font-medium text-slate-500">${dashboardEscapeHtml(displaysLabel)}</span>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Last Connected') }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center justify-center rounded-full border px-2.5 py-1 text-[11px] font-bold ${connection.pill}">
                                ${dashboardEscapeHtml(connection.label)}
                            </span>
                            <span class="text-[12px] font-semibold text-slate-700">${dashboardEscapeHtml(item.lastConnected || '-')}</span>
                        </div>
                        <p class="mt-2 text-[12px] leading-5 text-slate-500">${dashboardEscapeHtml(connection.helper)}</p>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-slate-50/70 px-4 py-3">
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Quick access') }}</p>
                        <p class="mt-1 truncate text-[12px] text-slate-500">{{ __('Open the workstation drawer or continue to the management page.') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'workstation', id: ${item.id} }}))"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold uppercase tracking-[0.14em] text-slate-600 transition hover:bg-slate-100"
                        >
                            ${hierarchyIcons.workstation}
                            Open
                        </button>
                    </div>
                </div>
            </article>
        `;
    }

    async function renderDashboardWorkstationModalPage(page = 1, limit = 10) {
        const holder = document.getElementById('dashboard-stat-modal-grid');
        if (!holder) {
            return;
        }

        const state = window.dashboardWorkstationModalState;
        state.page = page;
        state.limit = limit;

        await loadDashboardWorkstationFacilities();
        await loadDashboardWorkstationWorkgroups(state.facilityId);

        const query = new URLSearchParams({
            page: String(page),
            limit: String(limit)
        });
        if (state.search) {
            query.set('search', state.search);
        }
        if (state.facilityId) {
            query.set('facility_id', state.facilityId);
        }
        if (state.workgroupId) {
            query.set('workgroup_id', state.workgroupId);
        }

        holder.innerHTML = `
            <div class="flex min-h-[16rem] items-center justify-center px-6 py-12 text-sm font-medium text-slate-500">
                ${@json(__('Loading workstation records...'))}
            </div>
        `;

        try {
            const response = await fetch(`/api/workstations?${query.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const payload = await response.json();
            const items = payload.data || [];
            const total = Number(payload.total || 0);
            const totalPages = Math.max(1, Math.ceil(total / limit));
            const currentPage = Math.min(Math.max(page, 1), totalPages);
            const from = total === 0 ? 0 : ((currentPage - 1) * limit) + 1;
            const to = total === 0 ? 0 : Math.min(currentPage * limit, total);
            const paginationItems = dashboardPaginationItems(currentPage, totalPages);
            const facilities = state.facilities || [];
            const workgroups = state.workgroups || [];

            holder.innerHTML = `
                <div class="px-6 py-5">
                    <div class="mb-5 rounded-[1.6rem] border border-slate-200 bg-slate-50/70 p-4">
                        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                            <div class="max-w-xl">
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Filter workstations') }}</p>
                                <h4 class="mt-1 text-lg font-bold tracking-tight text-slate-900">${@json(__('Narrow the active scope'))}</h4>
                                <p class="mt-1 text-[13px] text-slate-500">{{ __('Filter by facility, workgroup, or a workstation name before you open the detailed record.') }}</p>
                            </div>
                            <button type="button" data-workstation-modal-reset class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Reset filters
                            </button>
                        </div>
                        <div class="mt-4 grid gap-3 xl:grid-cols-[1.2fr_0.9fr_0.9fr]">
                            <label class="block">
                                <span class="mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Search') }}</span>
                                <input data-workstation-modal-search type="text" value="${dashboardEscapeHtml(state.search)}" placeholder="Search workstation name..." class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-sky-300 focus:ring-2 focus:ring-sky-100">
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Facility') }}</span>
                                <select data-workstation-modal-facility class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition focus:border-sky-300 focus:ring-2 focus:ring-sky-100">
                                    <option value="">{{ __('All facilities') }}</option>
                                    ${facilities.map((facility) => `<option value="${facility.id}" ${String(state.facilityId) === String(facility.id) ? 'selected' : ''}>${dashboardEscapeHtml(facility.name)}</option>`).join('')}
                                </select>
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Workgroup') }}</span>
                                <select data-workstation-modal-workgroup class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition focus:border-sky-300 focus:ring-2 focus:ring-sky-100">
                                    <option value="">{{ __('All workgroups') }}</option>
                                    ${workgroups.map((workgroup) => `<option value="${workgroup.id}" ${String(state.workgroupId) === String(workgroup.id) ? 'selected' : ''}>${dashboardEscapeHtml(workgroup.name)}</option>`).join('')}
                                </select>
                            </label>
                        </div>
                    </div>
                    ${items.length
                        ? `<div class="grid gap-4 xl:grid-cols-2">${items.map(renderDashboardWorkstationCard).join('')}</div>`
                        : `<div class="flex min-h-[18rem] flex-col items-center justify-center rounded-[1.75rem] border border-dashed border-slate-200 bg-slate-50/70 px-6 py-12 text-center">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 shadow-sm">
                                    <i data-lucide="layout-grid" class="h-7 w-7"></i>
                                </div>
                                <h4 class="mt-4 text-lg font-bold text-slate-900">${@json(__('No workstation records found'))}</h4>
                                <p class="mt-2 max-w-md text-sm text-slate-500">${@json(__('There are no workstation records available in the current dashboard scope.'))}</p>
                           </div>`
                    }
                </div>
                <div class="border-t border-slate-200/90 px-6 py-4">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs font-medium text-slate-500">${@json(__('Showing'))} ${from} ${@json(__('to'))} ${to} ${@json(__('of'))} ${total} ${@json(__('results'))}</p>
                        <div class="flex flex-wrap items-center justify-end gap-2">
                            <button type="button" ${currentPage === 1 ? 'disabled' : ''} data-workstation-modal-page="${currentPage - 1}" class="inline-flex h-9 items-center justify-center rounded-full px-4 text-sm font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 disabled:pointer-events-none disabled:opacity-40">${@json(__('Prev'))}</button>
                            ${paginationItems.map((item) => {
                                if (item === 'ellipsis') {
                                    return '<span class="px-1 text-sm font-semibold text-slate-400">...</span>';
                                }
                                const active = item === currentPage;
                                return `<button type="button" data-workstation-modal-page="${item}" class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded-full border px-3 text-sm font-semibold transition ${active ? 'border-sky-500 bg-sky-500 text-white shadow-sm' : 'border-transparent text-slate-600 hover:border-slate-200 hover:bg-slate-50 hover:text-slate-900'}">${item}</button>`;
                            }).join('')}
                            <button type="button" ${currentPage >= totalPages ? 'disabled' : ''} data-workstation-modal-page="${currentPage + 1}" class="inline-flex h-9 items-center justify-center rounded-full px-4 text-sm font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 disabled:pointer-events-none disabled:opacity-40">${@json(__('Next'))}</button>
                        </div>
                    </div>
                </div>
            `;

            holder.querySelectorAll('[data-workstation-modal-page]').forEach((button) => {
                button.addEventListener('click', () => {
                    const nextPage = Number(button.getAttribute('data-workstation-modal-page'));
                    if (!Number.isNaN(nextPage) && nextPage >= 1 && nextPage <= totalPages) {
                        renderDashboardWorkstationModalPage(nextPage, limit);
                    }
                });
            });

            const searchInput = holder.querySelector('[data-workstation-modal-search]');
            const facilitySelect = holder.querySelector('[data-workstation-modal-facility]');
            const workgroupSelect = holder.querySelector('[data-workstation-modal-workgroup]');
            const resetButton = holder.querySelector('[data-workstation-modal-reset]');

            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    clearTimeout(window.dashboardWorkstationSearchTimer);
                    window.dashboardWorkstationModalState.search = searchInput.value.trim();
                    window.dashboardWorkstationSearchTimer = setTimeout(() => {
                        renderDashboardWorkstationModalPage(1, limit);
                    }, 250);
                });
            }

            if (facilitySelect) {
                facilitySelect.addEventListener('change', async () => {
                    window.dashboardWorkstationModalState.facilityId = facilitySelect.value;
                    window.dashboardWorkstationModalState.workgroupId = '';
                    await loadDashboardWorkstationWorkgroups(facilitySelect.value);
                    renderDashboardWorkstationModalPage(1, limit);
                });
            }

            if (workgroupSelect) {
                workgroupSelect.addEventListener('change', () => {
                    window.dashboardWorkstationModalState.workgroupId = workgroupSelect.value;
                    renderDashboardWorkstationModalPage(1, limit);
                });
            }

            if (resetButton) {
                resetButton.addEventListener('click', async () => {
                    window.dashboardWorkstationModalState.search = '';
                    window.dashboardWorkstationModalState.facilityId = '';
                    window.dashboardWorkstationModalState.workgroupId = '';
                    await loadDashboardWorkstationWorkgroups('');
                    renderDashboardWorkstationModalPage(1, limit);
                });
            }

            if (window.lucide) {
                window.lucide.createIcons();
            }
        } catch (error) {
            holder.innerHTML = `
                <div class="flex min-h-[16rem] flex-col items-center justify-center px-6 py-12 text-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 shadow-sm">
                        <i data-lucide="triangle-alert" class="h-7 w-7"></i>
                    </div>
                    <h4 class="mt-4 text-lg font-bold text-slate-900">${@json(__('Unable to load workstation records'))}</h4>
                    <p class="mt-2 max-w-md text-sm text-slate-500">${@json(__('Please try again or open the full workstation page for a broader view.'))}</p>
                </div>
            `;
            if (window.lucide) {
                window.lucide.createIcons();
            }
        }
    }

    const dashboardGridClasses = {
        table: 'w-full text-sm text-left',
        th: 'px-4 py-3 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500 border-b border-slate-100 bg-transparent',
        td: 'px-4 py-2.5 border-b border-slate-100 align-middle bg-transparent',
        container: 'text-slate-700',
        pagination: 'flex items-center justify-between px-5 py-4 text-xs font-medium text-slate-500'
    };

    const dashboardGridStyles = {
        table: { border: 'none' },
        th: { background: '#f8fbff', boxShadow: 'none' },
        td: { background: 'transparent' },
        footer: { background: 'transparent' }
    };

    function dashboardDeferRender(fn, timeout = 160) {
        if (typeof window.requestIdleCallback === 'function') {
            window.requestIdleCallback(() => fn(), { timeout });
            return;
        }
        window.setTimeout(fn, Math.min(timeout, 120));
    }

    window.dashboardGridsRendered = false;
    window.dashboardRefreshInFlight = false;
    window.currentDashboardStatModalKey = null;
    const canManageDashboardTasks = @json($dashboardJs['canManageDashboardTasks']);
    const dashboardNoActionsLabel = @json($dashboardJs['noActions']);
    const dashboardScheduleTaskLabel = @json($dashboardJs['scheduleTask']);
    const dashboardDeleteTaskLabel = @json($dashboardJs['deleteTask']);
    const dashboardGridText = @json($dashboardJs['grid']);
    const dashboardHistorySummaryText = @json($dashboardJs['historySummary']);

    const dashboardHistorySummaryModal = {
        activeId: null,
        init() {
            this.root = document.getElementById('dashboard-history-summary-modal');
            if (!this.root || this.initialized) return;
            this.initialized = true;
            this.overlay = this.root.querySelector('[data-dashboard-history-summary-overlay]');
            this.panel = this.root.querySelector('[data-dashboard-history-summary-panel]');
            this.body = document.getElementById('dashboard-history-summary-body');
            this.title = document.getElementById('dashboard-history-summary-title');
            this.subtitle = document.getElementById('dashboard-history-summary-subtitle');
            this.printLink = document.getElementById('dashboard-history-summary-print');

            this.root.querySelectorAll('[data-dashboard-history-summary-close]').forEach((button) => {
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
            this.title.textContent = name || dashboardHistorySummaryText.summaryTitle;
            this.subtitle.textContent = dashboardHistorySummaryText.loadingReportSummary;
            this.body.innerHTML = `<div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">${dashboardEscapeHtml(dashboardHistorySummaryText.loadingReportSummary)}</div>`;
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
            return `<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset ${cls}">${dashboardEscapeHtml(label || '-')}</span>`;
        },
        renderInfoGrid(items) {
            if (!items?.length) return '';
            return `<section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">${items.map((item) => `
                <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">${dashboardEscapeHtml(item.label || '-')}</p>
                    <p class="mt-2 break-words text-sm font-medium text-slate-800">${dashboardEscapeHtml(item.value || '-')}</p>
                </div>`).join('')}</section>`;
        },
        renderSection(section) {
            const scores = Array.isArray(section.scores) ? section.scores : [];
            const questions = Array.isArray(section.questions) ? section.questions : [];
            const comment = section.comment || '';
            return `
                <section class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_12px_40px_-32px_rgba(15,23,42,0.24)]">
                    <h3 class="text-lg font-semibold text-slate-900">${dashboardEscapeHtml(section.name || dashboardHistorySummaryText.section)}</h3>
                    <p class="mt-1 text-sm text-slate-500">${dashboardEscapeHtml(dashboardHistorySummaryText.reviewScoredChecks)}</p>
                    ${scores.length ? `
                        <div class="mt-5 overflow-hidden rounded-[1.25rem] border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr class="text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">
                                        <th class="px-4 py-3">${dashboardEscapeHtml(dashboardHistorySummaryText.score)}</th>
                                        <th class="px-4 py-3">${dashboardEscapeHtml(dashboardHistorySummaryText.limit)}</th>
                                        <th class="px-4 py-3">${dashboardEscapeHtml(dashboardHistorySummaryText.measured)}</th>
                                        <th class="px-4 py-3">${dashboardEscapeHtml(dashboardHistorySummaryText.status)}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    ${scores.map((score) => `
                                        <tr>
                                            <td class="px-4 py-3 font-medium text-slate-800">${dashboardEscapeHtml(score.name || '-')}</td>
                                            <td class="px-4 py-3 text-slate-600">${dashboardEscapeHtml(score.limit || '-')}</td>
                                            <td class="px-4 py-3 text-slate-600">${dashboardEscapeHtml(score.measured || '-')}</td>
                                            <td class="px-4 py-3">${this.renderBadge(score.statusLabel || '-', score.statusTone || 'neutral')}</td>
                                        </tr>`).join('')}
                                </tbody>
                            </table>
                        </div>` : ''}
                    ${questions.length ? `<div class="mt-5 grid gap-3">${questions.map((question) => `
                        <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-sm font-medium text-slate-800">${dashboardEscapeHtml(question.text || '-')}</p>
                                ${this.renderBadge(question.answer || '-', question.tone || 'neutral')}
                            </div>
                        </div>`).join('')}</div>` : ''}
                    ${comment ? `
                        <div class="mt-5 rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">${dashboardEscapeHtml(dashboardHistorySummaryText.comment)}</p>
                            <p class="mt-2 text-sm leading-6 text-slate-700">${dashboardEscapeHtml(comment)}</p>
                        </div>` : ''}
                </section>`;
        },
        render(payload) {
            this.title.textContent = payload.name || dashboardHistorySummaryText.summaryTitle;
            this.subtitle.textContent = `${payload.performedAt || '-'} • ${payload.display?.display || '-'}`;
            this.printLink.setAttribute('href', payload.printUrl || '#');
            const displayInfo = [
                { label: dashboardHistorySummaryText.facility, value: payload.display?.facility || '-' },
                { label: dashboardHistorySummaryText.workgroup, value: payload.display?.workgroup || '-' },
                { label: dashboardHistorySummaryText.workstation, value: payload.display?.workstation || '-' },
                { label: dashboardHistorySummaryText.displayLabel, value: payload.display?.display || '-' },
                { label: dashboardHistorySummaryText.performedAt, value: payload.performedAt || '-' },
                { label: dashboardHistorySummaryText.result, value: payload.resultLabel || '-' },
            ];
            this.body.innerHTML = `
                <div class="space-y-5">
                    <section class="flex flex-wrap items-center gap-3 rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-4">
                        ${this.renderBadge(payload.resultLabel || 'Unknown', payload.resultTone || 'neutral')}
                        <span class="text-sm text-slate-500">${dashboardEscapeHtml(dashboardHistorySummaryText.detailedSummaryForTask)}</span>
                    </section>
                    ${this.renderInfoGrid(displayInfo)}
                    ${payload.header?.length ? this.renderInfoGrid(payload.header) : ''}
                    ${payload.sections?.length ? payload.sections.map((section) => this.renderSection(section)).join('') : `<div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">${dashboardEscapeHtml(dashboardHistorySummaryText.noStructuredSummary)}</div>`}
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
                this.body.innerHTML = `<div class="rounded-[1.5rem] border border-rose-200 bg-rose-50 px-4 py-8 text-center text-sm text-rose-600">${dashboardEscapeHtml(dashboardHistorySummaryText.failedToLoadHistorySummary)}</div>`;
            }
        }
    };

    window.refreshDashboardGrids = function () {
        if (window.dashboardRefreshInFlight) {
            return;
        }
        window.dashboardRefreshInFlight = true;

        ['failed-displays-grid', 'latest-performed-grid', 'due-tasks-grid'].forEach((id) => {
            const target = document.getElementById(id);
            if (target) {
                target.innerHTML = '';
            }
        });

        window.dashboardGridsRendered = false;
        window.renderDashboardGrids();
        window.setTimeout(() => {
            window.dashboardRefreshInFlight = false;
        }, 600);
    };

    function renderDashboardDueTaskActions(row) {
        if (!canManageDashboardTasks) {
            return `<span class="text-xs text-slate-400">${dashboardNoActionsLabel}</span>`;
        }

        return `
            <div class="relative flex justify-end">
                <button
                    type="button"
                    data-dashboard-due-task-toggle="${row.id}"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700"
                >
                    <i data-lucide="more-vertical" class="h-4 w-4"></i>
                </button>
                <div data-dashboard-due-task-menu="${row.id}" class="absolute right-0 top-full z-20 mt-2 hidden w-44 overflow-hidden rounded-2xl border border-slate-200 bg-white py-1 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                    <button type="button" data-dashboard-due-task-edit="${row.id}" class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i data-lucide="calendar-clock" class="h-4 w-4 text-sky-500"></i>
                        ${dashboardScheduleTaskLabel}
                    </button>
                    <button type="button" data-dashboard-due-task-delete="${row.id}" class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                        ${dashboardDeleteTaskLabel}
                    </button>
                </div>
            </div>
        `;
    }

    function closeDashboardDueTaskMenus() {
        document.querySelectorAll('[data-dashboard-due-task-menu]').forEach((menu) => menu.classList.add('hidden'));
    }

    async function deleteDashboardDueTask(id) {
        const formData = new FormData();
        formData.append('_token', @json($dashboardJs['csrfToken']));
        formData.append('id', id);

        try {
            const payload = await Perfectlum.postForm(@json($dashboardJs['deleteTaskUrl']), formData);
            if (!payload.success) {
                notify('failed', payload.msg || 'Failed to delete task.');
                return;
            }

            notify('success', payload.msg || 'Task deleted successfully.');
            if (window.currentDashboardStatModalKey === 'due_tasks') {
                window.openDashboardStatModal('due_tasks');
            }
        } catch (error) {
            notify('failed', 'Failed to delete task.');
        }
    }

    window.hierarchyIcons = window.hierarchyIcons || {
        workstation: '<svg class="h-3.5 w-3.5 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
        workgroup: '<svg class="h-3.5 w-3.5 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        facility: '<svg class="h-3.5 w-3.5 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M8 10h.01"/><path d="M16 10h.01"/><path d="M8 14h.01"/><path d="M16 14h.01"/></svg>',
        display: '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
        history: '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
    };
    const hierarchyIcons = window.hierarchyIcons;

    const dashboardStatModalConfigs = {
        displays_ok: {
            eyebrow: @json($dashboardJs['modals']['displays_ok']['eyebrow']),
            title: @json($dashboardJs['modals']['displays_ok']['title']),
            description: @json($dashboardJs['modals']['displays_ok']['description']),
            fullUrl: @json($dashboardJs['modals']['displays_ok']['fullUrl']),
            baseUrl: (page = 1, limit = 10) => `/api/displays?type=ok&page=${page}&limit=${limit}`,
            columns: [
                {
                    name: @json(__('Display')),
                    formatter: (cell) => gridjs.html(`
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${cell.displayId} }}))" class="min-w-[16rem] text-left text-[13px] font-bold text-slate-900 transition hover:text-sky-600">
                            ${dashboardEscapeHtml(cell.displayName)}
                        </button>
                    `)
                },
                {
                    name: dashboardGridText.workstation,
                    formatter: (cell, row) => gridjs.html(dashboardHierarchyButton('workstation', row.cells[0].data.wsId, cell, hierarchyIcons.workstation))
                },
                {
                    name: @json(__('Workgroup')),
                    formatter: (cell, row) => gridjs.html(dashboardHierarchyButton('workgroup', row.cells[0].data.wgId, cell, hierarchyIcons.workgroup))
                },
                {
                    name: @json(__('Facility')),
                    formatter: (cell, row) => gridjs.html(dashboardHierarchyButton('facility', row.cells[0].data.facId, cell, hierarchyIcons.facility))
                },
                {
                    name: @json(__('Status')),
                    formatter: () => gridjs.html(`<span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-emerald-700">${dashboardGridText.ok}</span>`)
                }
            ],
            server: {
                url: (page = 1, limit = 10) => `/api/displays?type=ok&page=${page}&limit=${limit}`,
                then: (data) => {
                    setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 50);
                    return (data.data || []).map((item) => [
                        {
                            displayId: item.id,
                            displayName: item.displayName,
                            wsId: item.wsId,
                            wgId: item.wgId,
                            facId: item.facId
                        },
                        item.wsName,
                        item.wgName,
                        item.facName,
                        item.status
                    ]);
                },
                total: (data) => data.total || 0
            }
        },
        displays_failed: {
            eyebrow: @json($dashboardJs['modals']['displays_failed']['eyebrow']),
            title: @json($dashboardJs['modals']['displays_failed']['title']),
            description: @json($dashboardJs['modals']['displays_failed']['description']),
            fullUrl: @json($dashboardJs['modals']['displays_failed']['fullUrl']),
            baseUrl: (page = 1, limit = 10) => `/api/displays?type=failed&sort=updated_at&order=desc&page=${page}&limit=${limit}`,
            columns: [
                {
                    name: @json(__('Display')),
                    formatter: (cell) => gridjs.html(`
                        <div class="flex min-h-[2.5rem] items-center">
                            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${cell.displayId} }}))" class="min-w-[16rem] text-left text-[13px] font-bold leading-5 text-slate-900 transition hover:text-sky-600">
                                ${dashboardEscapeHtml(cell.displayName)}
                            </button>
                        </div>
                    `)
                },
                {
                    name: @json(__('Workstation')),
                    formatter: (cell, row) => gridjs.html(`
                        <div class="flex min-h-[2.5rem] items-center">
                            ${dashboardHierarchyButton('workstation', row.cells[0].data.wsId, cell, hierarchyIcons.workstation)}
                        </div>
                    `)
                },
                {
                    name: @json(__('Workgroup')),
                    formatter: (cell, row) => gridjs.html(`
                        <div class="flex min-h-[2.5rem] items-center">
                            ${dashboardHierarchyButton('workgroup', row.cells[0].data.wgId, cell, hierarchyIcons.workgroup)}
                        </div>
                    `)
                },
                {
                    name: @json(__('Facility')),
                    formatter: (cell, row) => gridjs.html(`
                        <div class="flex min-h-[2.5rem] items-center">
                            ${dashboardHierarchyButton('facility', row.cells[0].data.facId, cell, hierarchyIcons.facility)}
                        </div>
                    `)
                },
                {
                    name: dashboardGridText.errorDetails,
                    formatter: (cell) => gridjs.html(`
                        <div class="flex min-h-[2.5rem] items-center">
                            <div class="max-w-[22rem] whitespace-normal text-sm font-medium leading-6 text-rose-600">${dashboardEscapeHtml(cell)}</div>
                        </div>
                    `)
                },
                {
                    name: @json(__('Status')),
                    formatter: () => gridjs.html(`
                        <div class="flex min-h-[2.5rem] items-center">
                            <span class="inline-flex rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-rose-700">${dashboardGridText.fail}</span>
                        </div>
                    `)
                }
            ],
            server: {
                url: (page = 1, limit = 10) => `/api/displays?type=failed&sort=updated_at&order=desc&page=${page}&limit=${limit}`,
                then: (data) => {
                    setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 50);
                    return (data.data || []).map((item) => [
                        {
                            displayId: item.id,
                            displayName: item.displayName,
                            wsId: item.wsId,
                            wgId: item.wgId,
                            facId: item.facId
                        },
                        item.wsName,
                        item.wgName,
                        item.facName,
                        item.attentionText || dashboardDisplayErrorText(item.errors),
                        item.status
                    ]);
                },
                total: (data) => data.total || 0
            }
        },
        workstations: {
            eyebrow: @json($dashboardJs['modals']['workstations']['eyebrow']),
            title: @json($dashboardJs['modals']['workstations']['title']),
            description: @json($dashboardJs['modals']['workstations']['description']),
            fullUrl: @json($dashboardJs['modals']['workstations']['fullUrl']),
            baseUrl: (page = 1, limit = 10) => `/api/workstations?page=${page}&limit=${limit}`,
            customRenderer: true
        },
        due_tasks: {
            eyebrow: @json($dashboardJs['modals']['due_tasks']['eyebrow']),
            title: @json($dashboardJs['modals']['due_tasks']['title']),
            description: @json($dashboardJs['modals']['due_tasks']['description']),
            fullUrl: @json($dashboardJs['modals']['due_tasks']['fullUrl']),
            baseUrl: (page = 1, limit = 10) => `/api/due-tasks?page=${page}&limit=${limit}`,
            columns: [
                {
                    name: @json(__('Task Name')),
                    formatter: (cell) => gridjs.html(`
                        <div class="min-w-[14rem] max-w-[15rem] whitespace-normal text-[13px] font-bold leading-5 text-slate-900">
                            ${dashboardEscapeHtml(cell)}
                        </div>
                    `)
                },
                {
                    name: @json(__('Display')),
                    formatter: (cell) => gridjs.html(`
                        <div class="min-w-[22rem]">
                            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${cell.displayId} }}))" class="text-left text-[13px] font-bold text-slate-900 transition hover:text-sky-600">
                                ${dashboardEscapeHtml(cell.displayName)}
                            </button>
                            <div class="mt-1 flex items-center gap-2 whitespace-nowrap text-[11px] leading-5 text-slate-500">
                                ${dashboardHierarchyButton('workstation', cell.wsId, cell.wsName, hierarchyIcons.workstation)}
                                <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                ${dashboardHierarchyButton('workgroup', cell.wgId, cell.wgName, hierarchyIcons.workgroup)}
                                <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                ${dashboardHierarchyButton('facility', cell.facId, cell.facName, hierarchyIcons.facility)}
                            </div>
                        </div>
                    `)
                },
                {
                    name: @json(__('Schedule Type')),
                    formatter: (cell) => gridjs.html(`
                        <div class="flex justify-start text-left">
                            <span class="inline-flex items-center justify-center rounded-full bg-amber-50 px-2.5 py-1 text-center text-[11px] font-bold text-amber-700">${dashboardEscapeHtml(cell)}</span>
                        </div>
                    `)
                },
                {
                    name: @json(__('Due Date')),
                    formatter: (cell) => gridjs.html(`<span class="whitespace-nowrap text-sm font-semibold text-slate-700">${dashboardEscapeHtml(cell.formatted)}</span>`)
                },
                {
                    name: @json(__('Status')),
                    formatter: (cell) => {
                        const tone = cell.statusColor === 'danger'
                            ? 'border-rose-200 bg-rose-50 text-rose-700'
                            : (cell.statusColor === 'warning'
                                ? 'border-amber-200 bg-amber-50 text-amber-700'
                                : 'border-emerald-200 bg-emerald-50 text-emerald-700');
                        return gridjs.html(`
                            <div class="flex justify-center text-center">
                                <span class="inline-flex items-center justify-center rounded-full border px-2.5 py-1 text-center text-[11px] font-bold uppercase tracking-[0.14em] ${tone}">${dashboardEscapeHtml(cell.label)}</span>
                            </div>
                        `);
                    }
                },
                    {
                        name: @json(__('Actions')),
                        sort: false,
                        formatter: (cell, row) => gridjs.html(`
                            <div class="flex items-center justify-end gap-2">
                                ${renderDashboardDueTaskActions(row.cells[1].data)}
                            </div>
                        `)
                    }
                ],
                server: {
                url: (page = 1, limit = 10) => `/api/due-tasks?page=${page}&limit=${limit}`,
                then: (data) => {
                    setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 50);
                    return (data.data || []).map((item) => [
                        item.taskName,
                        {
                            id: item.id,
                            displayId: item.displayId,
                            displayName: item.displayName,
                            wsId: item.wsId,
                            wsName: item.wsName,
                            wgId: item.wgId,
                            wgName: item.wgName,
                            facId: item.facId,
                            facName: item.facName
                        },
                        item.scheduleName,
                        {
                            formatted: item.dueAt
                        },
                        {
                            label: item.status,
                            statusColor: item.statusColor
                        },
                        null
                    ]);
                },
                total: (data) => data.total || 0
            }
        }
    };

    window.closeDashboardStatModal = function () {
        const modal = document.getElementById('dashboard-stat-modal');
        const panel = document.getElementById('dashboard-stat-modal-panel');
        if (!modal || !panel) {
            return;
        }

        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0', 'pointer-events-none');
        panel.classList.remove('translate-y-0', 'scale-100');
        panel.classList.add('translate-y-3', 'scale-[0.985]');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    };

    window.openDashboardStatModal = function (key) {
        const config = dashboardStatModalConfigs[key];
        const modal = document.getElementById('dashboard-stat-modal');
        const panel = document.getElementById('dashboard-stat-modal-panel');
        const gridHolder = document.getElementById('dashboard-stat-modal-grid');

        const needsGrid = !config?.customRenderer;
        if (!config || !modal || !panel || !gridHolder || (needsGrid && (typeof gridjs === 'undefined' || typeof Perfectlum === 'undefined'))) {
            return;
        }

        window.currentDashboardStatModalKey = key;

        document.getElementById('dashboard-stat-modal-eyebrow').textContent = config.eyebrow;
        document.getElementById('dashboard-stat-modal-title').textContent = config.title;
        document.getElementById('dashboard-stat-modal-description').textContent = config.description;
        document.getElementById('dashboard-stat-modal-link').href = config.fullUrl;

        if (config.customRenderer) {
            gridHolder.innerHTML = '<div class="w-full"></div>';
            renderDashboardWorkstationModalPage(1, 10);
        } else {
            const mountId = `dashboard-stat-modal-grid-${Date.now()}`;
            gridHolder.innerHTML = `<div id="${mountId}" class="w-full"></div>`;
            const gridTarget = document.getElementById(mountId);

            Perfectlum.createGrid(gridTarget, {
                columns: config.columns,
                server: {
                    url: config.server.url(1, 10),
                    then: config.server.then,
                    total: config.server.total
                },
                sort: false,
                search: false,
                pagination: {
                    enabled: true,
                    limit: 10,
                    server: {
                        url: (prev, page, limit) => {
                            const base = prev.split('?')[0];
                            const params = new URLSearchParams(prev.split('?')[1] || '');
                            params.set('page', page + 1);
                            params.set('limit', limit);
                            return `${base}?${params.toString()}`;
                        }
                    }
                },
                className: dashboardGridClasses,
                style: dashboardGridStyles
            });
        }

        modal.classList.remove('hidden', 'pointer-events-none', 'opacity-0');
        modal.classList.add('opacity-100');

        requestAnimationFrame(() => {
            panel.classList.remove('translate-y-3', 'scale-[0.985]');
            panel.classList.add('translate-y-0', 'scale-100');
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    };

    window.renderDashboardGrids = function () {
        if (window.dashboardGridsRendered) {
            return;
        }

        if (typeof gridjs === 'undefined' || typeof Perfectlum === 'undefined') {
            setTimeout(window.renderDashboardGrids, 100);
            return;
        }

        if (document.getElementById('failed-displays-grid')) {
            Perfectlum.createGrid(document.getElementById('failed-displays-grid'), {
                columns: [
                    {
                    name: dashboardGridText.display,
                        formatter: (cell) => gridjs.html(`
                            <div class="min-w-[14rem]">
                                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${cell.displayId} }}))" class="line-clamp-1 text-left text-[13px] font-bold leading-5 text-slate-900 transition hover:text-sky-600">
                                    ${dashboardEscapeHtml(cell.displayName)}
                                </button>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    ${dashboardHierarchyButton('workstation', cell.wsId, cell.wsName, hierarchyIcons.workstation)}
                                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                    ${dashboardHierarchyButton('workgroup', cell.wgId, cell.wgName, hierarchyIcons.workgroup)}
                                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                    ${dashboardHierarchyButton('facility', cell.facId, cell.facName, hierarchyIcons.facility)}
                                </div>
                            </div>
                        `)
                    },
                    {
                    name: @json(__('Workstation')),
                        formatter: (cell) => gridjs.html(`<span class="text-sm font-semibold text-slate-700">${dashboardEscapeHtml(cell)}</span>`)
                    },
                    {
                    name: dashboardGridText.lastUpdate,
                        formatter: (cell) => gridjs.html(`<span class="whitespace-nowrap text-xs font-semibold text-slate-500">${dashboardEscapeHtml(cell)}</span>`)
                    },
                    {
                    name: dashboardGridText.errorDetails,
                        formatter: (cell) => gridjs.html(`<div class="max-w-[16rem] whitespace-normal text-[12px] font-semibold leading-5 text-rose-600">${dashboardEscapeHtml(cell)}</div>`)
                    },
                    {
                    name: dashboardGridText.actions,
                        sort: false,
                        formatter: (cell, row) => gridjs.html(`
                            <div class="flex items-center justify-end gap-2">
                                ${dashboardActionButton({
                                    onClick: `window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${row.cells[0].data.displayId} } }))`,
                                    tone: 'blue',
                                    icon: hierarchyIcons.display
                                })}
                                ${dashboardActionButton({
                                    href: dashboardHistoryUrl(row.cells[0].data.displayId),
                                    tone: 'amber',
                                    icon: hierarchyIcons.history
                                })}
                            </div>
                        `)
                    }
                ],
                server: {
                    url: '/api/displays?type=failed&sort=updated_at&order=desc&limit=10&page=1',
                    then: (data) => (data.data || []).map((item) => [
                        {
                            displayId: item.id,
                            displayName: item.displayName,
                            wsId: item.wsId,
                            wsName: item.wsName,
                            wgId: item.wgId,
                            wgName: item.wgName,
                            facId: item.facId,
                            facName: item.facName,
                        },
                        item.wsName,
                        item.updatedAt,
                        item.attentionText || dashboardDisplayErrorText(item.errors),
                        null,
                    ])
                },
                sort: false,
                pagination: false,
                search: false,
                className: dashboardGridClasses,
                style: dashboardGridStyles
            });
        }

        if (document.getElementById('latest-performed-grid')) {
            Perfectlum.createGrid(document.getElementById('latest-performed-grid'), {
                columns: [
                    {
                    name: dashboardGridText.task,
                        formatter: (cell) => gridjs.html(`
                            <div class="min-w-[10rem]">
                                <button type="button" data-dashboard-history-open="${cell.historyId}" data-dashboard-history-name="${dashboardEscapeHtml(cell.name)}" class="line-clamp-2 block text-left text-[13px] font-bold leading-5 text-slate-900 transition hover:text-sky-600 hover:underline">
                                    ${dashboardEscapeHtml(cell.name)}
                                </button>
                                <p class="mt-0.5 truncate text-[11px] font-medium text-slate-500">${dashboardEscapeHtml(cell.displayName)}</p>
                            </div>
                        `)
                    },
                    {
                    name: dashboardGridText.result,
                        formatter: (cell) => {
                            const tone = cell === 'ok'
                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                : 'bg-rose-50 text-rose-700 border-rose-200';
                            const label = cell === 'ok' ? dashboardGridText.pass : dashboardGridText.fail;
                            return gridjs.html(`<span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.14em] ${tone}">${label}</span>`);
                        }
                    },
                    {
                    name: dashboardGridText.performed,
                        formatter: (cell) => gridjs.html(`<span class="whitespace-nowrap text-xs font-semibold text-slate-500">${dashboardEscapeHtml(cell)}</span>`)
                    }
                ],
                server: {
                    url: '/api/latest-performed?limit=8',
                    then: (data) => data.map((item) => [
                        {
                            historyId: item.historyId,
                            name: item.name,
                            displayName: item.displayName,
                        },
                        item.result,
                        item.timeFormatted,
                    ])
                },
                sort: false,
                pagination: false,
                search: false,
                className: dashboardGridClasses,
                style: dashboardGridStyles
            });
        }

        const dueGridTarget = document.getElementById('due-tasks-grid');
        const watchlistTarget = document.getElementById('connection-watchlist-widget');

        if (watchlistTarget) {
            dashboardDeferRender(() => renderDashboardConnectionWatchlist(5), 180);
        }

        if (dueGridTarget) {
            dashboardDeferRender(() => {
                Perfectlum.createGrid(dueGridTarget, {
                columns: [
                    {
                    name: dashboardGridText.taskName,
                        formatter: (cell) => gridjs.html(`
                            <div class="min-w-[12rem] max-w-[14rem] whitespace-normal text-[13px] font-bold leading-5 text-slate-900">
                                ${dashboardEscapeHtml(cell)}
                            </div>
                        `)
                    },
                    {
                    name: dashboardGridText.display,
                        formatter: (cell) => gridjs.html(`
                            <div class="min-w-[18rem] max-w-[22rem]">
                                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${cell.displayId} }}))" class="line-clamp-1 text-left text-[13px] font-bold leading-5 text-slate-900 transition hover:text-sky-600">
                                    ${dashboardEscapeHtml(cell.displayName)}
                                </button>
                                <div class="mt-1 flex items-center gap-1.5 whitespace-nowrap text-[11px] leading-4 text-slate-500">
                                    ${dashboardHierarchyButton('workstation', cell.wsId, cell.wsName, hierarchyIcons.workstation)}
                                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                    ${dashboardHierarchyButton('workgroup', cell.wgId, cell.wgName, hierarchyIcons.workgroup)}
                                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                    ${dashboardHierarchyButton('facility', cell.facId, cell.facName, hierarchyIcons.facility)}
                                </div>
                            </div>
                        `)
                    },
                    {
                    name: dashboardGridText.scheduleType,
                        formatter: (cell) => gridjs.html(`<span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700">${dashboardEscapeHtml(cell)}</span>`)
                    },
                    {
                    name: dashboardGridText.dueDate,
                        formatter: (cell) => gridjs.html(`<span class="whitespace-nowrap text-xs font-semibold text-slate-700">${dashboardEscapeHtml(cell.formatted)}</span>`)
                    },
                    {
                    name: dashboardGridText.status,
                        formatter: (cell) => {
                            const tones = {
                                overdue: 'bg-rose-50 text-rose-700 border-rose-200',
                                today: 'bg-amber-50 text-amber-700 border-amber-200',
                                upcoming: 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            };
                            const label = cell === 'overdue'
                                ? dashboardGridText.overdue
                                : (cell === 'today' ? dashboardGridText.today : dashboardGridText.upcoming);
                            return gridjs.html(`<span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.14em] ${tones[cell]}">${label}</span>`);
                        }
                    },
                    {
                    name: dashboardGridText.actions,
                        sort: false,
                        formatter: (cell, row) => gridjs.html(`
                            <div class="flex items-center justify-end gap-2">
                                ${dashboardActionButton({
                                    onClick: `window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${row.cells[1].data.displayId} } }))`,
                                    tone: 'blue',
                                    icon: hierarchyIcons.display
                                })}
                            </div>
                        `)
                    }
                ],
                server: {
                    url: '/api/due-tasks?limit=8&without_total=1',
                    then: (data) => (data.data || []).map((item) => [
                        item.taskName,
                        {
                            id: item.id,
                            displayId: item.displayId,
                            displayName: item.displayName,
                            wsId: item.wsId,
                            wsName: item.wsName,
                            wgId: item.wgId,
                            wgName: item.wgName,
                            facId: item.facId,
                            facName: item.facName
                        },
                        item.scheduleName,
                        {
                            formatted: item.dueAt
                        },
                        item.dueColor === 'danger' ? 'overdue' : (item.dueColor === 'warning' ? 'today' : 'upcoming'),
                        null
                    ])
                },
                sort: false,
                pagination: false,
                search: false,
                className: dashboardGridClasses,
                style: dashboardGridStyles
            });
            }, 240);
        }

        window.dashboardGridsRendered = true;
    };

    const dashboardNativeModalConfig = {
        displays_ok: {
            endpoint: (page = 1, limit = 10) => `/api/displays?type=ok&page=${page}&limit=${limit}`,
            fullUrl: @json($dashboardJs['modals']['displays_ok']['fullUrl']),
            eyebrow: @json($dashboardJs['modals']['displays_ok']['eyebrow']),
            title: @json($dashboardJs['modals']['displays_ok']['title']),
            description: @json($dashboardJs['modals']['displays_ok']['description']),
        },
        displays_failed: {
            endpoint: (page = 1, limit = 10) => `/api/displays?type=failed&sort=updated_at&order=desc&page=${page}&limit=${limit}`,
            fullUrl: @json($dashboardJs['modals']['displays_failed']['fullUrl']),
            eyebrow: @json($dashboardJs['modals']['displays_failed']['eyebrow']),
            title: @json($dashboardJs['modals']['displays_failed']['title']),
            description: @json($dashboardJs['modals']['displays_failed']['description']),
        },
        due_tasks: {
            endpoint: (page = 1, limit = 10) => `/api/due-tasks?page=${page}&limit=${limit}`,
            fullUrl: @json($dashboardJs['modals']['due_tasks']['fullUrl']),
            eyebrow: @json($dashboardJs['modals']['due_tasks']['eyebrow']),
            title: @json($dashboardJs['modals']['due_tasks']['title']),
            description: @json($dashboardJs['modals']['due_tasks']['description']),
        },
        workstations: {
            fullUrl: @json($dashboardJs['modals']['workstations']['fullUrl']),
            eyebrow: @json($dashboardJs['modals']['workstations']['eyebrow']),
            title: @json($dashboardJs['modals']['workstations']['title']),
            description: @json($dashboardJs['modals']['workstations']['description']),
            custom: true,
        }
    };

    window.dashboardNativeModalState = { key: null, page: 1, limit: 10, total: 0 };

    function dashboardNativeTable(columns, rows, maxHeightClass = 'max-h-[30rem]') {
        return `
            <div class="dashboard-editorial-scroll ${maxHeightClass}">
                <table class="dashboard-editorial-table min-w-full">
                    <thead><tr>${columns.map((col) => {
                        const label = typeof col === 'string' ? col : (col.label || '');
                        const center = typeof col === 'object' && !!col.center;
                        return `<th class="${center ? 'text-center' : ''}">${dashboardEscapeHtml(label)}</th>`;
                    }).join('')}</tr></thead>
                    <tbody>${rows.join('')}</tbody>
                </table>
            </div>
        `;
    }

    function dashboardNativeEmpty(message) {
        return `<div class="rounded-2xl border border-slate-200 bg-slate-50 px-6 py-10 text-center text-sm text-slate-500">${dashboardEscapeHtml(message)}</div>`;
    }

    function dashboardFetchJsonNative(url) {
        return fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then((r) => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            });
    }

    function dashboardResultBadgeNative(result) {
        const ok = String(result || '').toLowerCase() === 'ok';
        return ok
            ? '<span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] text-emerald-700">Pass</span>'
            : '<span class="inline-flex items-center rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] text-rose-700">Fail</span>';
    }

    function dashboardDueBadgeNative(color) {
        if (color === 'danger') return `<span class="inline-flex items-center rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] text-rose-700">${dashboardGridText.overdue}</span>`;
        if (color === 'warning') return `<span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] text-amber-700">${dashboardGridText.today}</span>`;
        return `<span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] text-emerald-700">${dashboardGridText.upcoming}</span>`;
    }

    function renderDashboardNativeModalPage(page = 1, limit = 10) {
        const state = window.dashboardNativeModalState;
        const config = dashboardNativeModalConfig[state.key];
        const holder = document.getElementById('dashboard-stat-modal-grid');
        if (!holder || !config) return;
        if (config.custom) {
            holder.innerHTML = '<div class="w-full"></div>';
            renderDashboardWorkstationModalPage(page, limit);
            return;
        }

        holder.innerHTML = dashboardNativeEmpty(@json(__('Loading records...')));
        dashboardFetchJsonNative(config.endpoint(page, limit)).then((payload) => {
            const items = payload.data || [];
            const total = Number(payload.total || items.length || 0);
            state.page = page;
            state.limit = limit;
            state.total = total;
            let columns = [];
            let rows = [];

            if (state.key === 'displays_ok') {
                columns = [@json(__('Display')), @json(__('Workstation')), @json(__('Workgroup')), @json(__('Facility')), @json(__('Status'))];
                rows = items.map((item) => `<tr>
                    <td><button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${Number(item.id || 0)} }}))" class="text-left text-[13px] font-bold text-slate-900 transition hover:text-sky-600">${dashboardEscapeHtml(item.displayName || '-')}</button></td>
                    <td>${dashboardHierarchyButton('workstation', Number(item.wsId || 0), item.wsName || '-', hierarchyIcons.workstation)}</td>
                    <td>${dashboardHierarchyButton('workgroup', Number(item.wgId || 0), item.wgName || '-', hierarchyIcons.workgroup)}</td>
                    <td>${dashboardHierarchyButton('facility', Number(item.facId || 0), item.facName || '-', hierarchyIcons.facility)}</td>
                    <td class="text-center"><span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] text-emerald-700">${dashboardGridText.ok}</span></td>
                </tr>`);
            } else if (state.key === 'displays_failed') {
                columns = [@json(__('Display')), @json(__('Workstation')), @json(__('Workgroup')), @json(__('Facility')), dashboardGridText.errorDetails, @json(__('Status'))];
                rows = items.map((item) => `<tr>
                    <td><button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${Number(item.id || 0)} }}))" class="text-left text-[13px] font-bold leading-5 text-slate-900 transition hover:text-sky-600">${dashboardEscapeHtml(item.displayName || '-')}</button></td>
                    <td>${dashboardHierarchyButton('workstation', Number(item.wsId || 0), item.wsName || '-', hierarchyIcons.workstation)}</td>
                    <td>${dashboardHierarchyButton('workgroup', Number(item.wgId || 0), item.wgName || '-', hierarchyIcons.workgroup)}</td>
                    <td>${dashboardHierarchyButton('facility', Number(item.facId || 0), item.facName || '-', hierarchyIcons.facility)}</td>
                    <td><div class="max-w-[18rem] whitespace-normal text-[12px] font-semibold leading-5 text-rose-600">${dashboardEscapeHtml(item.attentionText || dashboardDisplayErrorText(item.errors))}</div></td>
                    <td class="text-center"><span class="inline-flex rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] text-rose-700">${dashboardGridText.fail}</span></td>
                </tr>`);
            } else if (state.key === 'due_tasks') {
                columns = [@json(__('Task Name')), @json(__('Display')), @json(__('Schedule Type')), @json(__('Due Date')), @json(__('Status')), @json(__('Actions'))];
                rows = items.map((item) => `<tr>
                    <td><div class="max-w-[15rem] whitespace-normal text-[13px] font-bold leading-5 text-slate-900">${dashboardEscapeHtml(item.taskName || '-')}</div></td>
                    <td>
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${Number(item.displayId || 0)} }}))" class="text-left text-[13px] font-bold text-slate-900 transition hover:text-sky-600">${dashboardEscapeHtml(item.displayName || '-')}</button>
                        <div class="mt-1 flex items-center gap-1.5 whitespace-nowrap text-[11px] leading-4 text-slate-500">
                            ${dashboardHierarchyButton('workstation', Number(item.wsId || 0), item.wsName || '-', hierarchyIcons.workstation)}
                            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                            ${dashboardHierarchyButton('workgroup', Number(item.wgId || 0), item.wgName || '-', hierarchyIcons.workgroup)}
                            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                            ${dashboardHierarchyButton('facility', Number(item.facId || 0), item.facName || '-', hierarchyIcons.facility)}
                        </div>
                    </td>
                    <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700">${dashboardEscapeHtml(item.scheduleName || '-')}</span></td>
                    <td class="text-center whitespace-nowrap text-xs font-semibold text-slate-700">${dashboardEscapeHtml(item.dueAt || '-')}</td>
                    <td class="text-center">${dashboardDueBadgeNative(item.statusColor)}</td>
                    <td class="text-center"><div class="flex items-center justify-center gap-2">${renderDashboardDueTaskActions(item)}</div></td>
                </tr>`);
            }

            const totalPages = Math.max(1, Math.ceil(total / limit));
            holder.innerHTML = dashboardNativeTable(columns, rows.length ? rows : [`<tr><td colspan="${columns.length}" class="px-6 py-8">${dashboardNativeEmpty(@json(__('No records found')))}</td></tr>`], 'max-h-[58vh]') + `
                <div class="mt-4 flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold text-slate-500">${((page - 1) * limit) + 1}-${Math.min(page * limit, total)} / ${total}</p>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="window.changeDashboardNativeModalPage(-1)" ${page <= 1 ? 'disabled' : ''} class="inline-flex h-9 items-center rounded-full border border-slate-200 px-3 text-xs font-bold uppercase tracking-[0.12em] text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40">Prev</button>
                        <span class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">${page} / ${totalPages}</span>
                        <button type="button" onclick="window.changeDashboardNativeModalPage(1)" ${page >= totalPages ? 'disabled' : ''} class="inline-flex h-9 items-center rounded-full border border-slate-200 px-3 text-xs font-bold uppercase tracking-[0.12em] text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40">Next</button>
                    </div>
                </div>
            `;
            if (window.lucide) window.lucide.createIcons();
        }).catch(() => {
            holder.innerHTML = dashboardNativeEmpty(@json(__('Unable to load records. Please try again.')));
        });
    }

    window.changeDashboardNativeModalPage = function (delta) {
        const state = window.dashboardNativeModalState;
        const totalPages = Math.max(1, Math.ceil((state.total || 0) / (state.limit || 10)));
        const nextPage = Math.min(totalPages, Math.max(1, state.page + delta));
        if (nextPage === state.page) return;
        renderDashboardNativeModalPage(nextPage, state.limit);
    };

    const dashboardInvestigate = {
        activeDisplayId: null,
        init() {
            this.root = document.getElementById('dashboard-investigate-drawer');
            if (!this.root || this.initialized) return;
            if (this.root.parentElement !== document.body) {
                document.body.appendChild(this.root);
            }
            this.initialized = true;
            this.overlay = this.root.querySelector('[data-dashboard-investigate-overlay]');
            this.panel = this.root.querySelector('[data-dashboard-investigate-panel]');
            this.title = document.getElementById('dashboard-investigate-title');
            this.subtitle = document.getElementById('dashboard-investigate-subtitle');
            this.body = document.getElementById('dashboard-investigate-body');

            this.overlay?.addEventListener('click', () => this.close());
            this.root.querySelectorAll('[data-dashboard-investigate-close]').forEach((btn) => {
                btn.addEventListener('click', () => this.close());
            });
        },
        openSkeleton(displayId, displayName) {
            this.activeDisplayId = Number(displayId || 0);
            this.title.textContent = displayName || 'Display Investigation';
            this.subtitle.textContent = 'Loading latest sync, hierarchy, and recent results...';
            this.body.innerHTML = '<div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">Loading investigation data...</div>';
            this.root.classList.remove('hidden');
            requestAnimationFrame(() => {
                this.overlay?.classList.remove('opacity-0');
                this.panel?.classList.remove('translate-x-full');
            });
            if (window.lucide) window.lucide.createIcons();
        },
        close() {
            if (!this.root || this.root.classList.contains('hidden')) return;
            this.overlay?.classList.add('opacity-0');
            this.panel?.classList.add('translate-x-full');
            setTimeout(() => this.root.classList.add('hidden'), 200);
        },
        resultBadge(tone, label) {
            const map = {
                success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
                danger: 'border-rose-200 bg-rose-50 text-rose-700',
                warning: 'border-amber-200 bg-amber-50 text-amber-700',
            };
            const cls = map[tone] || 'border-slate-200 bg-slate-50 text-slate-700';
            return `<span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] ${cls}">${dashboardEscapeHtml(label || '-')}</span>`;
        },
        render(payload) {
            const hierarchy = payload?.hierarchy || {};
            const history = payload?.history || {};
            const recent = Array.isArray(history.recent) ? history.recent.slice(0, 5) : [];
            const links = payload?.links || {};
            const latestError = payload?.latestError || payload?.statusSummary || '-';
            const lastSync = payload?.lastSync || '-';
            const wsConnected = payload?.connectedLabel || '-';

            this.subtitle.textContent = `Last sync ${lastSync} • ${wsConnected}`;
            this.body.innerHTML = `
                <div class="space-y-4">
                    <section class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Latest Error Signal</p>
                        <p class="mt-2 text-sm font-semibold leading-6 text-rose-600">${dashboardEscapeHtml(latestError)}</p>
                        <p class="mt-2 text-xs font-semibold text-slate-500">Last Activity: ${dashboardEscapeHtml(lastSync)}</p>
                    </section>

                    <section class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Scope</p>
                        <div class="mt-3 space-y-2 text-sm text-slate-700">
                            <div class="flex items-center gap-2"><span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-sky-500 text-[9px] font-black text-white">F</span><span class="truncate">${dashboardEscapeHtml(hierarchy?.facility?.name || '-')}</span></div>
                            <div class="flex items-center gap-2"><span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-sky-500 text-[8px] font-black text-white">WG</span><span class="truncate">${dashboardEscapeHtml(hierarchy?.workgroup?.name || '-')}</span></div>
                            <div class="flex items-center gap-2"><span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-sky-500 text-[8px] font-black text-white">WS</span><span class="truncate">${dashboardEscapeHtml(hierarchy?.workstation?.name || '-')}</span></div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-slate-200 bg-white p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Recent Test Results</p>
                            <a href="${dashboardEscapeHtml(links.histories || '#')}" class="text-xs font-bold uppercase tracking-[0.12em] text-sky-700 hover:underline">Open History</a>
                        </div>
                        <div class="mt-3 space-y-2">
                            ${recent.length ? recent.map((item) => `
                                <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-800">${dashboardEscapeHtml(item.name || '-')}</p>
                                        <p class="mt-0.5 text-xs font-medium text-slate-500">${dashboardEscapeHtml(item.performedAt || '-')}</p>
                                    </div>
                                    ${this.resultBadge(item.resultTone, item.resultLabel)}
                                </div>
                            `).join('') : '<div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-4 text-center text-xs text-slate-500">No recent results</div>'}
                        </div>
                    </section>

                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <a href="${dashboardEscapeHtml(links.settings || '#')}" class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold uppercase tracking-[0.12em] text-slate-700 hover:bg-slate-50">Open Display Detail</a>
                        <a href="${dashboardEscapeHtml(links.calibration || '#')}" class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold uppercase tracking-[0.12em] text-slate-700 hover:bg-slate-50">Open Calibration</a>
                    </div>
                </div>
            `;
            if (window.lucide) window.lucide.createIcons();
        },
        async load(displayId, displayName) {
            this.openSkeleton(displayId, displayName);
            try {
                const payload = await dashboardFetchJsonNative(`/api/display-modal/${Number(displayId || 0)}`);
                if (this.activeDisplayId !== Number(displayId || 0)) return;
                this.render(payload || {});
            } catch (error) {
                this.body.innerHTML = '<div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-8 text-center text-sm text-rose-600">Unable to load investigation details.</div>';
            }
        }
    };

    const dashboardTaskDetailModal = {
        init() {
            this.root = document.getElementById('dashboard-task-detail-modal');
            if (!this.root || this.initialized) return;
            if (this.root.parentElement !== document.body) {
                document.body.appendChild(this.root);
            }
            this.initialized = true;
            this.overlay = this.root.querySelector('[data-dashboard-task-overlay]');
            this.panel = this.root.querySelector('[data-dashboard-task-panel]');
            this.overlay?.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.close();
            });
            this.root.querySelectorAll('[data-dashboard-task-close]').forEach((btn) => {
                btn.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    this.close();
                });
            });
        },
        statusBadge(color) {
            if (color === 'danger') return '<span class="inline-flex rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] text-rose-700">Overdue</span>';
            if (color === 'warning') return '<span class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] text-amber-700">Today</span>';
            return '<span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] text-emerald-700">Upcoming</span>';
        },
        bindScopeButton(id, type, value) {
            const btn = document.getElementById(id);
            if (!btn) return;
            btn.onclick = () => {
                if (!value?.id) return;
                window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type, id: value.id } }));
            };
        },
        open(item) {
            if (!this.root || !this.panel) return;
            document.getElementById('dashboard-task-title').textContent = item.taskName || 'Task detail';
            document.getElementById('dashboard-task-subtitle').textContent = `Schedule ${item.scheduleName || '-'} • Due ${item.dueAt || '-'}`;
            document.getElementById('dashboard-task-name').textContent = item.taskName || '-';
            document.getElementById('dashboard-task-schedule').textContent = item.scheduleName || '-';
            document.getElementById('dashboard-task-due').textContent = item.dueAt || '-';
            document.getElementById('dashboard-task-status').innerHTML = this.statusBadge(item.dueColor || '');
            document.getElementById('dashboard-task-display').textContent = item.displayName || '-';
            document.getElementById('dashboard-task-facility').textContent = item.facName || '-';
            document.getElementById('dashboard-task-workgroup').textContent = item.wgName || '-';
            document.getElementById('dashboard-task-workstation').textContent = item.wsName || '-';

            const displayBtn = document.getElementById('dashboard-task-display-link');
            if (displayBtn) {
                displayBtn.onclick = () => {
                    if (!item.displayId) return;
                    window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: item.displayId } }));
                };
            }
            this.bindScopeButton('dashboard-task-facility-link', 'facility', { id: item.facId });
            this.bindScopeButton('dashboard-task-workgroup-link', 'workgroup', { id: item.wgId });
            this.bindScopeButton('dashboard-task-workstation-link', 'workstation', { id: item.wsId });

            this.root.classList.remove('hidden');
            document.body.classList.add('overflow-hidden', 'dashboard-task-modal-open');
            requestAnimationFrame(() => {
                this.overlay?.classList.remove('opacity-0');
                this.panel?.classList.remove('opacity-0', 'scale-[0.985]', 'translate-y-4');
            });
            if (window.lucide) window.lucide.createIcons();
        },
        close() {
            if (!this.root || this.root.classList.contains('hidden')) return;
            this.overlay?.classList.add('opacity-0');
            this.panel?.classList.add('opacity-0', 'scale-[0.985]', 'translate-y-4');
            document.body.classList.remove('dashboard-task-modal-open');
            setTimeout(() => {
                this.root.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 200);
        }
    };

    window.openDashboardStatModal = function (key) {
        const config = dashboardNativeModalConfig[key];
        const modal = document.getElementById('dashboard-stat-modal');
        const panel = document.getElementById('dashboard-stat-modal-panel');
        const holder = document.getElementById('dashboard-stat-modal-grid');
        if (!config || !modal || !panel || !holder) return;

        window.currentDashboardStatModalKey = key;
        window.dashboardNativeModalState = { key, page: 1, limit: 10, total: 0 };
        document.getElementById('dashboard-stat-modal-eyebrow').textContent = config.eyebrow;
        document.getElementById('dashboard-stat-modal-title').textContent = config.title;
        document.getElementById('dashboard-stat-modal-description').textContent = config.description;
        document.getElementById('dashboard-stat-modal-link').href = config.fullUrl;
        renderDashboardNativeModalPage(1, 10);

        modal.classList.remove('hidden', 'pointer-events-none', 'opacity-0');
        modal.classList.add('opacity-100');
        requestAnimationFrame(() => {
            panel.classList.remove('translate-y-3', 'scale-[0.985]');
            panel.classList.add('translate-y-0', 'scale-100');
            if (window.lucide) window.lucide.createIcons();
        });
    };

    window.renderDashboardGrids = function () {
        if (window.dashboardGridsRendered) return;

        const failedTarget = document.getElementById('failed-displays-grid');
        const latestTarget = document.getElementById('latest-performed-grid');
        const dueTarget = document.getElementById('due-tasks-grid');

        if (failedTarget) {
            dashboardDeferRender(() => {
                dashboardFetchJsonNative('/api/displays?type=failed&sort=updated_at&order=desc&limit=5&page=1').then((payload) => {
                    const items = payload.data || [];
                    const columns = [
                        dashboardGridText.display,
                        @json(__('Scope')),
                        { label: dashboardGridText.actions, center: true },
                    ];
                    const rows = items.map((item) => `<tr>
                        <td class="w-[58%]">
                            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${Number(item.id || 0)} }}))" class="block text-left text-[13px] font-bold leading-5 text-slate-900 transition hover:text-sky-600">${dashboardEscapeHtml(item.displayName || '-')}</button>
                            <div class="mt-1 max-w-[30rem] whitespace-normal text-[12px] font-semibold leading-5 text-rose-600">${dashboardEscapeHtml(item.attentionText || dashboardDisplayErrorText(item.errors))}</div>
                            <div class="mt-1.5 text-[11px] font-semibold text-slate-500">
                                Last Activity: ${dashboardEscapeHtml(item.updatedAt || '-')}
                            </div>
                        </td>
                        <td class="w-[28%]">
                            <div class="flex max-w-[22rem] flex-col items-start gap-1.5 text-[11px] leading-4">
                                ${dashboardScopeInlineButton('facility', Number(item.facId || 0), item.facName || '-', 'F')}
                                ${dashboardScopeInlineButton('workgroup', Number(item.wgId || 0), item.wgName || '-', 'WG')}
                                ${dashboardScopeInlineButton('workstation', Number(item.wsId || 0), item.wsName || '-', 'WS')}
                            </div>
                        </td>
                        <td class="w-[14%] text-center">
                            <div class="flex items-center justify-center">
                                ${dashboardRenderInvestigateAction(item)}
                            </div>
                        </td>
                    </tr>`);
                    failedTarget.innerHTML = dashboardNativeTable(columns, rows.length ? rows : [`<tr><td colspan="3" class="px-6 py-8">${dashboardNativeEmpty(@json(__('No failed displays detected')))}</td></tr>`], 'h-[31rem]');
                    if (window.lucide) window.lucide.createIcons();
                }).catch(() => { failedTarget.innerHTML = dashboardNativeEmpty(@json(__('Unable to load failed displays'))); });
            }, 80);
        }

        if (latestTarget) {
            dashboardDeferRender(() => {
                dashboardFetchJsonNative('/api/latest-performed?limit=7').then((payload) => {
                    const items = Array.isArray(payload) ? payload : [];
                    const columns = [
                        dashboardGridText.task,
                        `${dashboardGridText.result} / ${dashboardGridText.performed}`,
                    ];
                    const rows = items.map((item) => `<tr>
                        <td class="w-[66%]">
                            <button type="button" data-dashboard-history-open="${Number(item.historyId || 0)}" data-dashboard-history-name="${dashboardEscapeHtml(item.name || '-')}" class="block text-left text-[13px] font-bold leading-5 text-slate-900 transition hover:text-sky-600 hover:underline">${dashboardEscapeHtml(item.name || '-')}</button>
                            <p class="mt-0.5 truncate text-[11px] font-medium text-slate-500">${dashboardEscapeHtml(item.displayName || '-')}</p>
                        </td>
                        <td class="w-[34%] text-left">
                            <div class="flex flex-col items-start gap-1.5">
                                ${dashboardResultBadgeNative(item.result)}
                                <span class="text-left text-xs font-semibold leading-5 text-slate-500">${dashboardEscapeHtml(item.timeFormatted || '-')}</span>
                            </div>
                        </td>
                    </tr>`);
                    latestTarget.innerHTML = dashboardNativeTable(columns, rows.length ? rows : [`<tr><td colspan="2" class="px-6 py-8">${dashboardNativeEmpty(@json(__('No recent activity')))}</td></tr>`], 'h-[31rem]');
                }).catch(() => { latestTarget.innerHTML = dashboardNativeEmpty(@json(__('Unable to load recent activity'))); });
            }, 120);
        }

        if (dueTarget) {
            dashboardDeferRender(() => {
                dashboardFetchJsonNative('/api/due-tasks?limit=8&without_total=1').then((payload) => {
                    const items = payload.data || [];
                    const columns = [
                        dashboardGridText.taskName,
                        dashboardGridText.display,
                        { label: dashboardGridText.scheduleType, center: true },
                        { label: dashboardGridText.dueDate, center: true },
                        { label: dashboardGridText.status, center: true },
                        { label: dashboardGridText.actions, center: true },
                    ];
                    const rows = items.map((item) => `<tr>
                        <td><div class="max-w-[14rem] whitespace-normal text-[13px] font-bold leading-5 text-slate-900">${dashboardEscapeHtml(item.taskName || '-')}</div></td>
                        <td>
                            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${Number(item.displayId || 0)} }}))" class="text-left text-[13px] font-bold leading-5 text-slate-900 transition hover:text-sky-600">${dashboardEscapeHtml(item.displayName || '-')}</button>
                            <div class="mt-1 flex items-center gap-2 whitespace-nowrap text-[11px] leading-4">
                                ${dashboardScopeMiniButton('facility', Number(item.facId || 0), item.facName || '-', 'F')}
                                ${dashboardScopeMiniButton('workgroup', Number(item.wgId || 0), item.wgName || '-', 'WG')}
                                ${dashboardScopeMiniButton('workstation', Number(item.wsId || 0), item.wsName || '-', 'WS')}
                            </div>
                        </td>
                        <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700">${dashboardEscapeHtml(item.scheduleName || '-')}</span></td>
                        <td class="whitespace-nowrap text-center text-xs font-semibold text-slate-700">${dashboardEscapeHtml(item.dueAt || '-')}</td>
                        <td class="text-center">${dashboardDueBadgeNative(item.dueColor)}</td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    type="button"
                                    data-dashboard-task-detail='${JSON.stringify({
                                        id: Number(item.id || 0),
                                        taskName: item.taskName || '-',
                                        scheduleName: item.scheduleName || '-',
                                        dueAt: item.dueAt || '-',
                                        dueColor: item.dueColor || '',
                                        displayId: Number(item.displayId || 0),
                                        displayName: item.displayName || '-',
                                        wsId: Number(item.wsId || 0),
                                        wsName: item.wsName || '-',
                                        wgId: Number(item.wgId || 0),
                                        wgName: item.wgName || '-',
                                        facId: Number(item.facId || 0),
                                        facName: item.facName || '-'
                                    }).replace(/'/g, '&#39;')}'
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-600 transition hover:bg-sky-100"
                                    title="Open task detail"
                                >
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`);
                    dueTarget.innerHTML = dashboardNativeTable(columns, rows.length ? rows : [`<tr><td colspan="6" class="px-6 py-8">${dashboardNativeEmpty(@json(__('No due tasks found')))}</td></tr>`], 'max-h-[36rem]');
                    if (window.lucide) window.lucide.createIcons();
                }).catch(() => { dueTarget.innerHTML = dashboardNativeEmpty(@json(__('Unable to load due tasks'))); });
            }, 240);
        }

        window.dashboardGridsRendered = true;
    };

    function initDashboardPage() {
        const gridRoot = document.getElementById('failed-displays-grid');
        if (!gridRoot || gridRoot.dataset.dashboardInitialized === '1') {
            return;
        }
        gridRoot.dataset.dashboardInitialized = '1';

        dashboardHistorySummaryModal.init();
        dashboardInvestigate.init();
        dashboardTaskDetailModal.init();
        const modal = document.getElementById('dashboard-stat-modal');
        const overlay = document.getElementById('dashboard-stat-modal-overlay');
        const closeButton = document.getElementById('dashboard-stat-modal-close');

        if (overlay) {
            overlay.addEventListener('click', () => window.closeDashboardStatModal && window.closeDashboardStatModal());
        }

        if (closeButton) {
            closeButton.addEventListener('click', () => window.closeDashboardStatModal && window.closeDashboardStatModal());
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                window.closeDashboardStatModal && window.closeDashboardStatModal();
            }
        });

        document.addEventListener('click', (event) => {
            const historyTrigger = event.target.closest('[data-dashboard-history-open]');
            const toggle = event.target.closest('[data-dashboard-due-task-toggle]');
            const editButton = event.target.closest('[data-dashboard-due-task-edit]');
            const deleteButton = event.target.closest('[data-dashboard-due-task-delete]');
            const investigateButton = event.target.closest('[data-dashboard-investigate]');
            const taskDetailButton = event.target.closest('[data-dashboard-task-detail]');

            if (historyTrigger) {
                event.preventDefault();
                event.stopPropagation();
                dashboardHistorySummaryModal.load(Number(historyTrigger.dataset.dashboardHistoryOpen), historyTrigger.dataset.dashboardHistoryName || dashboardHistorySummaryText.summaryTitle);
                return;
            }

            if (investigateButton) {
                event.preventDefault();
                event.stopPropagation();
                dashboardInvestigate.load(
                    Number(investigateButton.dataset.dashboardInvestigate || 0),
                    investigateButton.dataset.dashboardInvestigateName || 'Display Investigation'
                );
                return;
            }

            if (taskDetailButton) {
                event.preventDefault();
                event.stopPropagation();
                try {
                    const payload = JSON.parse(taskDetailButton.dataset.dashboardTaskDetail || '{}');
                    dashboardTaskDetailModal.open(payload);
                } catch (_e) {}
                return;
            }

            if (toggle) {
                const menu = document.querySelector(`[data-dashboard-due-task-menu="${toggle.dataset.dashboardDueTaskToggle}"]`);
                const willOpen = menu?.classList.contains('hidden');
                closeDashboardDueTaskMenus();
                if (menu && willOpen) {
                    menu.classList.remove('hidden');
                }
                return;
            }

            if (editButton) {
                closeDashboardDueTaskMenus();
                window.edit_task?.(null, editButton.dataset.dashboardDueTaskEdit);
                return;
            }

            if (deleteButton) {
                closeDashboardDueTaskMenus();
                window.openTaskDeleteConfirm?.({
                    onConfirm: () => deleteDashboardDueTask(deleteButton.dataset.dashboardDueTaskDelete)
                });
                return;
            }

            if (!event.target.closest('[data-dashboard-due-task-menu]')) {
                closeDashboardDueTaskMenus();
            }
        });

        window.addEventListener('task-saved', () => {
            if (window.currentDashboardStatModalKey === 'due_tasks') {
                window.openDashboardStatModal('due_tasks');
            }
        });

        window.renderDashboardGrids();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardPage, { once: true });
    } else {
        initDashboardPage();
    }

    window.dashboardPageMount = initDashboardPage;
    window.dashboardPageCleanup = function () {
        window.dashboardGridsRendered = false;
        window.currentDashboardStatModalKey = null;

        document.body.classList.remove('overflow-hidden', 'dashboard-task-modal-open');

        const statModal = document.getElementById('dashboard-stat-modal');
        if (statModal) {
            statModal.classList.add('hidden', 'pointer-events-none', 'opacity-0');
        }

        const statPanel = document.getElementById('dashboard-stat-modal-panel');
        if (statPanel) {
            statPanel.classList.add('translate-y-3', 'scale-[0.985]');
            statPanel.classList.remove('translate-y-0', 'scale-100');
        }

        const historySummaryRoot = document.getElementById('dashboard-history-summary-modal');
        if (historySummaryRoot) {
            historySummaryRoot.classList.add('hidden');
        }

        const investigateRoot = document.getElementById('dashboard-investigate-drawer');
        if (investigateRoot) {
            investigateRoot.classList.add('hidden');
        }

        const taskRoot = document.getElementById('dashboard-task-detail-modal');
        if (taskRoot) {
            taskRoot.classList.add('hidden');
        }
    };
    })();
</script>

@include('common.navigations.footer')
