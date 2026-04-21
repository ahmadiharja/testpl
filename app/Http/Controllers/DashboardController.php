<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{
    protected function applyCompletedOneShotTaskVisibilityGuard($query)
    {
        return $query->where(function ($q) {
            $q->whereNotIn('tasks.schtype', [
                    \App\Models\ScheduleType::STARTUP,
                    \App\Models\ScheduleType::ONCE,
                ])
                ->orWhereNull('tasks.lastrun')
                ->orWhere('tasks.lastrun', '<=', 0)
                ->orWhere(function ($activeOneShot) {
                    $activeOneShot->whereIn('tasks.schtype', [
                            \App\Models\ScheduleType::STARTUP,
                            \App\Models\ScheduleType::ONCE,
                        ])
                        ->where('tasks.nextrun', '>', 0);
                });
        });
    }

    protected function staleQaTaskCutoff(): string
    {
        return now()->subDays(60)->toDateTimeString();
    }

    protected function applyQaTaskVisibilityGuard($query)
    {
        $cutoff = $this->staleQaTaskCutoff();

        return $query->where(function ($q) use ($cutoff) {
            $q->where(function ($recentWorkstation) use ($cutoff) {
                $recentWorkstation->whereNotNull('workstations.last_connected')
                    ->where('workstations.last_connected', '>=', $cutoff);
            })->orWhere('qa_tasks.updated_at', '>=', $cutoff);
        });
    }

    protected function resolveRecordDueTimestamp($record): int
    {
        $rawDueAt = (int) ($record->due_at ?? 0);
        if ($rawDueAt > 0) {
            return $rawDueAt;
        }

        if (($record->record_type ?? null) !== 'task') {
            return 0;
        }

        $startdate = trim((string) ($record->startdate ?? $record->startdate1 ?? ''));
        $starttime = trim((string) ($record->starttime ?? ''));
        if ($startdate === '' || $starttime === '') {
            return 0;
        }

        try {
            return \Carbon\Carbon::createFromFormat(
                'Y-m-d H:i',
                "{$startdate} {$starttime}",
                $record->fac_timezone ?: config('app.timezone', 'UTC')
            )->utc()->timestamp;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    protected function formatRecordDueAt($record, int $timestamp): string
    {
        if ($timestamp <= 0) {
            return '-';
        }

        return \Carbon\Carbon::createFromTimestampUTC($timestamp)
            ->setTimezone($record->fac_timezone ?: config('app.timezone', 'UTC'))
            ->format('d M Y H:i');
    }

    protected function shouldShowSchedulerLastExecution($record): bool
    {
        if (($record->record_type ?? null) !== 'task') {
            return false;
        }

        $scheduleTypeId = (int) ($record->schedule_type_id ?? 0);

        return in_array($scheduleTypeId, [
            \App\Models\ScheduleType::DAILY,
            \App\Models\ScheduleType::WEEKLY,
            \App\Models\ScheduleType::MONTHLY,
            \App\Models\ScheduleType::QUARTERLY,
            \App\Models\ScheduleType::SEMIANNUAL,
            \App\Models\ScheduleType::ANNUAL,
        ], true);
    }

    protected function formatSchedulerLastExecutionAt($record): ?string
    {
        if (!$this->shouldShowSchedulerLastExecution($record)) {
            return null;
        }

        $lastRun = (int) ($record->lastrun ?? 0);
        if ($lastRun <= 0) {
            return 'Never';
        }

        return \Carbon\Carbon::createFromTimestampUTC($lastRun)
            ->setTimezone($record->fac_timezone ?: config('app.timezone', 'UTC'))
            ->format('d M Y H:i');
    }

    protected function resolveTaskTypeLabel(?string $taskTypeKey, ?string $taskName, ?string $recordType = null): string
    {
        $resolvedName = trim((string) ($taskName ?? ''));
        if ($resolvedName !== '') {
            return $resolvedName;
        }

        $fallbackMap = [
            'cal' => 'Calibration',
            'con' => 'Display Conformance',
            'awl' => 'Display Luminance Calibration',
            'vwl' => 'Display Luminance Conformance',
            'vun' => 'Display Verify Uniformity',
            'dtp' => 'Display Test Pattern',
            'ovt' => 'Online Visual Display Test',
            'mmi' => 'MQSA Monitor Inspection',
            'fbs' => 'Full Black Surface Test',
            'ela' => 'Everlight ANZ QA',
            'sur' => 'SMPTE US Rad',
            'icc' => 'Create ICC Profile',
        ];

        $key = trim((string) ($taskTypeKey ?? ''));
        if ($key !== '' && isset($fallbackMap[$key])) {
            return $fallbackMap[$key];
        }

        return trim((string) ($recordType ?? '')) !== '' ? (string) $recordType : 'Task';
    }

    protected function resolveReschedulableTaskTypeKey(?string $taskTypeKey, ?string $taskName): ?string
    {
        $key = trim((string) ($taskTypeKey ?? ''));
        if ($key !== '') {
            return $key;
        }

        $value = strtolower(trim((string) ($taskName ?? '')));
        if ($value === '') {
            return null;
        }

        if (str_contains($value, 'display test pattern')) return 'dtp';
        if (str_contains($value, 'mqsa')) return 'mmi';
        if (str_contains($value, 'everlight')) return 'ela';
        if (str_contains($value, 'full black surface')) return 'fbs';
        if (str_contains($value, 'smpte')) return 'sur';
        if (str_contains($value, 'create icc')) return 'icc';
        if (str_contains($value, 'calibration conformance') || str_contains($value, 'display conformance')) return 'con';
        if (str_contains($value, 'luminance calibration')) return 'awl';
        if (str_contains($value, 'luminance conformance')) return 'vwl';
        if (str_contains($value, 'uniformity')) return 'vun';
        if (str_contains($value, 'calibration')) return 'cal';

        return null;
    }

    protected function resolveSchedulerStatus($record, int $timestamp, bool $isPast): array
    {
        $statusMap = [0 => 'OK', 1 => 'Failed', 2 => 'Run Error'];
        $rawStatus = (int) ($record->status ?? 0);
        $lastRun = (int) ($record->lastrun ?? 0);

        if (($record->record_type ?? null) === 'task' && $rawStatus === 2 && $lastRun <= 0 && $timestamp > 0 && $isPast) {
            return ['label' => 'Due', 'tone' => 'danger'];
        }

        if ($rawStatus === 0 && $timestamp > 0 && $isPast) {
            return ['label' => 'Due', 'tone' => 'danger'];
        }

        return [
            'label' => $statusMap[$rawStatus] ?? 'Unknown',
            'tone' => $rawStatus === 0 ? 'success' : 'danger',
        ];
    }

    protected function unresolvedFailedDisplaySummaries(array $displayRows): array
    {
        if (empty($displayRows)) {
            return [];
        }

        $displayIds = collect($displayRows)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($displayIds)) {
            return [];
        }

        $summaries = [];
        $seenTests = [];

        $historyRows = DB::table('histories')
            ->select(['id', 'display_id', 'name', 'regulation', 'result', 'time'])
            ->whereIn('display_id', $displayIds)
            ->whereNull('deleted_at')
            ->orderBy('display_id')
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->get();

        foreach ($historyRows as $history) {
            $displayId = (int) $history->display_id;
            $testKey = trim((string) ($history->regulation ?? ''));
            if ($testKey === '') {
                $testKey = trim((string) ($history->name ?? ''));
            }
            if ($testKey === '') {
                $testKey = 'history:' . (int) $history->id;
            }

            $compoundKey = $displayId . '|' . mb_strtolower($testKey);
            if (isset($seenTests[$compoundKey])) {
                continue;
            }
            $seenTests[$compoundKey] = true;

            if ((int) $history->result === 2) {
                continue;
            }

            if (!isset($summaries[$displayId])) {
                $summaries[$displayId] = [
                    'display_id' => $displayId,
                    'unresolved_tests_count' => 0,
                    'latest_failed_history_name' => trim((string) ($history->name ?? '')) ?: $testKey,
                    'latest_failed_test_key' => $testKey,
                    'latest_failed_history_time' => (int) $history->time,
                    'latest_failed_history_id' => (int) $history->id,
                ];
            }

            $summaries[$displayId]['unresolved_tests_count']++;

            if ((int) $history->time > (int) $summaries[$displayId]['latest_failed_history_time']) {
                $summaries[$displayId]['latest_failed_history_name'] = trim((string) ($history->name ?? '')) ?: $testKey;
                $summaries[$displayId]['latest_failed_test_key'] = $testKey;
                $summaries[$displayId]['latest_failed_history_time'] = (int) $history->time;
                $summaries[$displayId]['latest_failed_history_id'] = (int) $history->id;
            }
        }

        return $summaries;
    }

    protected function scopedDisplayRowsForDashboard(?int $facilityId = null): array
    {
        return DB::table('displays')
            ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->join('display_preferences', function ($join) {
                $join->on('display_preferences.display_id', '=', 'displays.id')
                    ->where('display_preferences.name', '=', 'exclude')
                    ->where('display_preferences.value', '=', '0');
            })
            ->when($facilityId, fn($q) => $q->where('workgroups.facility_id', $facilityId))
            ->select([
                'displays.id',
                'displays.manufacturer',
                'displays.model',
                'displays.serial',
                'displays.status',
                'displays.errors',
                'displays.updated_at',
                'displays.workstation_id',
                'workstations.name as ws_name',
                'workstations.workgroup_id as wg_id',
                'workgroups.name as wg_name',
                'workgroups.facility_id as fac_id',
                'facilities.name as fac_name',
                'facilities.timezone as fac_timezone',
            ])
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    protected function applyDashboardFailedDisplayMode(array $displayRows): array
    {
        $summaries = $this->unresolvedFailedDisplaySummaries($displayRows);

        return collect($displayRows)
            ->map(function (array $row) use ($summaries) {
                $summary = $summaries[(int) $row['id']] ?? null;
                $row['has_unresolved_failure'] = $summary !== null;
                $row['latest_failed_history_name'] = $summary['latest_failed_history_name'] ?? null;
                $row['latest_failed_history_time'] = $summary['latest_failed_history_time'] ?? null;
                $row['latest_failed_history_id'] = $summary['latest_failed_history_id'] ?? null;
                $row['unresolved_tests_count'] = $summary['unresolved_tests_count'] ?? 0;

                return $row;
            })
            ->all();
    }

    public function dashboard(Request $request){

        //$request->session()->put('id', '32');
        
        $data=array();
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role = $request->session()->get('role');
        
        //$workgroups_ids=\App\Models\Workgroup::where('user_id', $user_id)->pluck('id');
        //$workstations_ids=\App\Models\Workstation::whereIn('workgroup_id', $workgroups_ids)->pluck('id');
        
        //$displays_id=\App\Models\Display::whereIn('workstation_id', $workstations_ids)->pluck('id');

        $requestedFacilityId = $request->get('facility_id');
        $facility_id = $role === 'super'
            ? ($requestedFacilityId !== null && $requestedFacilityId !== '' ? $requestedFacilityId : null)
            : $user->facility_id;

        $dashboardDisplayRows = \Illuminate\Support\Facades\Cache::remember(
            'dashboard.display_rows.v2.' . md5(implode('|', [
                $user_id,
                $role ?: 'guest',
                $facility_id ?: 'all',
            ])),
            now()->addSeconds(20),
            fn () => $this->applyDashboardFailedDisplayMode(
                $this->scopedDisplayRowsForDashboard($facility_id ? (int) $facility_id : null)
            )
        );
        $totalVisibleDisplays = count($dashboardDisplayRows);
        $d_fail = collect($dashboardDisplayRows)->where('has_unresolved_failure', true)->count();
        $d_ok = max($totalVisibleDisplays - $d_fail, 0);
        
        $d_fail_recent=array(); $i=0;
            
        //$workstations=\App\Models\Workstation::whereIn('workgroup_id', $workgroups_ids)->count();
        $workstations = \App\Models\Workstation::when($facility_id, function ($q) use ($facility_id) {
            return $q->join('workgroups', 'workgroups.id', '=', 'workgroup_id')
                ->where('workgroups.facility_id', '=', $facility_id);
        })
            ->count();

        $staleThreshold = now()->subDays(7);
        $stale_workstations = \App\Models\Workstation::query()
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', '=', $facility_id))
            ->where(function ($q) use ($staleThreshold) {
                $q->whereNull('workstations.last_connected')
                    ->orWhere('workstations.last_connected', '<', $staleThreshold);
            })
            ->count();
        
        //$display_ids=\App\Models\Display::whereIn('workstation_id', $workstations_ids)->pluck('id');
        
        
        //total due tasks
        $ids = request()->input('id') ? explode(',', request()->input('id')) : [];
        $nowTimestamp = now()->timestamp;

        $taskFallbackDueExpression = "COALESCE(
            UNIX_TIMESTAMP(CONVERT_TZ(
                STR_TO_DATE(CONCAT(REPLACE(tasks.startdate, '.', '-'), ' ', LEFT(tasks.starttime, 5)), '%Y-%m-%d %H:%i'),
                COALESCE(facilities.timezone, 'UTC'),
                '+00:00'
            )),
            UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(REPLACE(tasks.startdate, '.', '-'), ' ', LEFT(tasks.starttime, 5)), '%Y-%m-%d %H:%i'))
        )";

        
        // Calculate due tasks count using direct joins so shared hosting does not
        // spend time on nested EXISTS chains for every dashboard hit.
        $tasksBase = DB::table('tasks')
            ->join('displays', 'displays.id', '=', 'tasks.display_id')
            ->join('display_preferences', function ($join) {
                $join->on('display_preferences.display_id', '=', 'displays.id')
                    ->where('display_preferences.name', '=', 'exclude')
                    ->where('display_preferences.value', '=', '0');
            })
            ->leftJoin('workstations', 'workstations.id', '=', 'displays.workstation_id')
            ->leftJoin('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->leftJoin('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->where('tasks.deleted', 0)
            ->where('tasks.disabled', 0)
            ->when($facility_id, function ($q) use ($facility_id) {
                return $q->where('workgroups.facility_id', '=', $facility_id);
            });

        $tasksBase = $this->applyCompletedOneShotTaskVisibilityGuard($tasksBase);

        $taskCountFast = (clone $tasksBase)
            ->where('tasks.nextrun', '>', 0)
            ->where('tasks.nextrun', '<=', $nowTimestamp)
            ->distinct('tasks.id')
            ->count('tasks.id');

        $taskCountFallback = (clone $tasksBase)
            ->where('tasks.nextrun', 0)
            ->whereNotNull('tasks.startdate')
            ->whereNotNull('tasks.starttime')
            ->where('tasks.startdate', '!=', '')
            ->where('tasks.starttime', '!=', '')
            ->whereRaw($taskFallbackDueExpression . ' > 0')
            ->whereRaw($taskFallbackDueExpression . ' <= ?', [$nowTimestamp])
            ->distinct('tasks.id')
            ->count('tasks.id');

        $qaTasksCount = DB::table('qa_tasks')
            ->join('displays', 'displays.id', '=', 'qa_tasks.display_id')
            ->join('display_preferences', function ($join) {
                $join->on('display_preferences.display_id', '=', 'displays.id')
                    ->where('display_preferences.name', '=', 'exclude')
                    ->where('display_preferences.value', '=', '0');
            })
            ->leftJoin('workstations', 'workstations.id', '=', 'displays.workstation_id')
            ->leftJoin('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->where('qa_tasks.deleted', 0)
            ->where('qa_tasks.nextdate', '>', 0)
            ->where('qa_tasks.nextdate', '<=', $nowTimestamp)
            ->when($facility_id, function ($q) use ($facility_id) {
                return $q->where('workgroups.facility_id', '=', $facility_id);
            });

        $qaTasksCount = $this->applyQaTaskVisibilityGuard($qaTasksCount)
            ->distinct('qa_tasks.id')
            ->count('qa_tasks.id');

        $due_tasks = $taskCountFast + $taskCountFallback + $qaTasksCount;
        $due_tasks_recents = $due_tasks;
            
        $greetingName = explode(' ', trim($user->name ?? 'Administrator'))[0] ?: 'Administrator';

        return view('dashboard.dashboard', [
            'user' => $user,
            'greetingName' => $greetingName,
            'd_ok' => $d_ok,
            'd_fail' => $d_fail,
            'workstations' => $workstations,
            'stale_workstations' => $stale_workstations,
            'due_tasks' => $due_tasks,
            'title' => 'Dashboard',
            'd_fail_recent' => $d_fail_recent,
            'due_tasks_recents' => $due_tasks_recents,
        ]);
    }

    public function hierarchy_modal_partial(Request $request)
    {
        return response()
            ->view('common.modals.hierarchical-location-modal')
            ->header('Cache-Control', 'private, max-age=120');
    }

    public function search(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        $role = $request->session()->get('role');

        $facilities = $role === 'super'
            ? \App\Models\Facility::orderBy('name')->get()
            : collect([$user->facility])->filter();

        return view('dashboard.search', [
            'title' => 'Search',
            'role' => $role,
            'facilities' => $facilities,
        ]);
    }

    public function api_global_search(Request $request)
    {
        $userId = $request->session()->get('id');
        $user = \App\Models\User::find($userId);
        $role = $request->session()->get('role');

        if (!$user) {
            return response()->json(['data' => []]);
        }

        $query = trim((string) $request->get('q', ''));
        if (mb_strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $limit = max(4, min((int) $request->get('limit', 12), 20));
        $perType = max(2, (int) ceil($limit / 4));
        $like = '%'.$query.'%';
        $facilityId = $role === 'super' ? null : $user->facility_id;

        $facilities = DB::table('facilities')
            ->select([
                'facilities.id',
                'facilities.name',
                'facilities.location',
                'facilities.timezone',
            ])
            ->when($facilityId, fn ($q) => $q->where('facilities.id', $facilityId))
            ->where('facilities.name', 'like', $like)
            ->orderBy('facilities.name')
            ->limit($perType)
            ->get()
            ->map(fn ($item) => [
                'id' => 'facility-'.$item->id,
                'recordId' => (int) $item->id,
                'facilityId' => (int) $item->id,
                'type' => 'facility',
                'title' => $item->name,
                'subtitle' => collect([$item->location, $item->timezone])->filter()->implode(' • '),
                'url' => url('facility-info/'.$item->id),
            ]);

        $workgroups = DB::table('workgroups')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->select([
                'workgroups.id',
                'workgroups.name',
                'workgroups.facility_id',
                'facilities.name as facility_name',
            ])
            ->when($facilityId, fn ($q) => $q->where('workgroups.facility_id', $facilityId))
            ->where('workgroups.name', 'like', $like)
            ->orderBy('workgroups.name')
            ->limit($perType)
            ->get()
            ->map(fn ($item) => [
                'id' => 'workgroup-'.$item->id,
                'recordId' => (int) $item->id,
                'facilityId' => (int) $item->facility_id,
                'facilityName' => $item->facility_name,
                'type' => 'workgroup',
                'title' => $item->name,
                'subtitle' => $item->facility_name,
                'url' => url('workgroups-info/'.$item->id),
            ]);

        $workstations = DB::table('workstations')
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->select([
                'workstations.id',
                'workstations.name',
                'workstations.workgroup_id',
                'workgroups.id as workgroup_id',
                'workgroups.name as workgroup_name',
                'facilities.id as facility_id',
                'facilities.name as facility_name',
            ])
            ->when($facilityId, fn ($q) => $q->where('workgroups.facility_id', $facilityId))
            ->where('workstations.name', 'like', $like)
            ->orderBy('workstations.name')
            ->limit($perType)
            ->get()
            ->map(fn ($item) => [
                'id' => 'workstation-'.$item->id,
                'recordId' => (int) $item->id,
                'facilityId' => (int) $item->facility_id,
                'facilityName' => $item->facility_name,
                'workgroupId' => (int) $item->workgroup_id,
                'workgroupName' => $item->workgroup_name,
                'type' => 'workstation',
                'title' => $item->name,
                'subtitle' => $item->workgroup_name.' • '.$item->facility_name,
                'url' => url('workstations-info/'.$item->id),
            ]);

        $displays = DB::table('displays')
            ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->leftJoin('display_preferences as excluded_pref', function ($join) {
                $join->on('excluded_pref.display_id', '=', 'displays.id')
                    ->where('excluded_pref.name', '=', 'exclude')
                    ->where('excluded_pref.value', '=', '1');
            })
            ->select([
                'displays.id',
                'displays.manufacturer',
                'displays.model',
                'displays.serial',
                'workstations.name as workstation_name',
                'workgroups.name as workgroup_name',
                'facilities.name as facility_name',
            ])
            ->when($facilityId, fn ($q) => $q->where('workgroups.facility_id', $facilityId))
            ->where(function ($q) use ($like) {
                $q->where('displays.manufacturer', 'like', $like)
                    ->orWhere('displays.model', 'like', $like)
                    ->orWhere('displays.serial', 'like', $like);
            })
            ->whereNull('excluded_pref.id')
            ->orderBy('displays.manufacturer')
            ->orderBy('displays.model')
            ->orderBy('displays.serial')
            ->limit($perType)
            ->get()
            ->map(function ($item) {
                $title = trim(
                    collect([$item->manufacturer, $item->model])
                        ->filter()
                        ->implode(' ')
                );

                if ($item->serial) {
                    $title .= ($title ? ' ' : '').'('.$item->serial.')';
                }

                return [
                    'id' => 'display-'.$item->id,
                    'recordId' => (int) $item->id,
                    'facilityName' => $item->facility_name,
                    'workgroupName' => $item->workgroup_name,
                    'workstationName' => $item->workstation_name,
                    'type' => 'display',
                    'title' => $title ?: 'Display #'.$item->id,
                    'subtitle' => collect([$item->workstation_name, $item->workgroup_name, $item->facility_name])->filter()->implode(' • '),
                    'url' => url('display-settings/'.$item->id),
                ];
            });

        $results = collect()
            ->merge($facilities)
            ->merge($workgroups)
            ->merge($workstations)
            ->merge($displays)
            ->take($limit)
            ->values();

        return response()->json([
            'data' => $results,
        ]);
    }
    
    public function update_sidebar(Request $request)
    {
        $user_id=$request->session()->get('id');
        $sidebar=$request->input('sidebar');
        
        $user=\App\Models\User::find($user_id);
        $user->sidebar=$sidebar;
        $user->save();
    }
   
    public function d_fail(Request $request)
    {
        return view('dashboard.d_fail', ['title' => 'Display Not Ok']);
    }
    
    public function d_ok(Request $request)
    {
        return view('dashboard.d_ok', ['title' => 'Display Ok']);
    }
    
    public function due_tasks(Request $request)
    {
        return view('dashboard.due_tasks', [
            'title' => 'Schedule Tasks',
            'role' => $request->session()->get('role'),
        ]);
    }

    public function client_monitor(Request $request)
    {
        $user = \App\Models\User::find($request->session()->get('id'));
        $role = (string) $request->session()->get('role', '');

        if (!$user || $role !== 'super') {
            abort(403);
        }

        return view('dashboard.client_monitor', [
            'title' => 'Client Monitor',
            'role' => $role,
        ]);
    }

    public function api_client_monitor(Request $request)
    {
        $user = \App\Models\User::find($request->session()->get('id'));
        $role = (string) $request->session()->get('role', '');
        if (!$user || $role !== 'super') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $requestedFacilityId = $request->get('facility_id');
        $facilityId = ($requestedFacilityId !== null && $requestedFacilityId !== '') ? (int) $requestedFacilityId : null;
        $lineLimit = max(20, min((int) $request->get('line_limit', 48), 120));
        $staleMinutes = max(5, min((int) $request->get('stale_minutes', 15), 180));
        $now = now();
        $staleThreshold = (clone $now)->subMinutes($staleMinutes);

        $workstationsBase = DB::table('workstations')
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->when($facilityId, fn($q) => $q->where('facilities.id', $facilityId));

        $displaysBase = DB::table('displays')
            ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->when($facilityId, fn($q) => $q->where('facilities.id', $facilityId));

        $workstationsTotal = (int) (clone $workstationsBase)->count();
        $workstationsOnline = (int) (clone $workstationsBase)->where('workstations.last_connected', '>=', $staleThreshold)->count();
        $workstationsOffline = max(0, $workstationsTotal - $workstationsOnline);
        $displaysTotal = (int) (clone $displaysBase)->count();
        $displaysConnected = (int) (clone $displaysBase)->where('displays.connected', 1)->count();
        $displaysDisconnected = (int) (clone $displaysBase)->where('displays.connected', 0)->count();
        $displaysFailed = (int) (clone $displaysBase)->where('displays.status', 2)->count();

        $historyBase = DB::table('histories')
            ->join('displays', 'displays.id', '=', 'histories.display_id')
            ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->when($facilityId, fn($q) => $q->where('facilities.id', $facilityId));

        $sinceEpoch = $now->copy()->subMinutes(10)->timestamp;
        $throughput = (clone $historyBase)
            ->where('histories.time', '>=', $sinceEpoch)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN histories.result = 2 THEN 1 ELSE 0 END) as success_count,
                SUM(CASE WHEN histories.result <> 2 THEN 1 ELSE 0 END) as failed_count
            ')
            ->first();

        $oneThird = max(8, (int) ceil($lineLimit / 3));
        $oneQuarter = max(6, (int) ceil($lineLimit / 4));

        $makeSnippet = static function ($value, int $max = 140): string {
            $text = trim(preg_replace('/\s+/', ' ', (string) $value));
            if ($text === '') {
                return '-';
            }
            if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                return mb_strlen($text) > $max ? (mb_substr($text, 0, $max - 3) . '...') : $text;
            }
            return strlen($text) > $max ? (substr($text, 0, $max - 3) . '...') : $text;
        };

        $events = collect();

        $heartbeatRows = (clone $workstationsBase)
            ->select([
                DB::raw('UNIX_TIMESTAMP(workstations.last_connected) as ts'),
                'workstations.id as workstation_id',
                'workstations.name as workstation_name',
                'workgroups.name as workgroup_name',
                'facilities.name as facility_name',
            ])
            ->whereNotNull('workstations.last_connected')
            ->orderBy('workstations.last_connected', 'desc')
            ->limit($oneThird)
            ->get();

        $events = $events->concat($heartbeatRows->map(function ($row) {
            return [
                'timestamp' => (int) ($row->ts ?? 0),
                'level' => 'INFO',
                'line' => sprintf(
                    '[HEARTBEAT] ws=%s(%d) wg=%s fac=%s',
                    $row->workstation_name ?: '-',
                    (int) $row->workstation_id,
                    $row->workgroup_name ?: '-',
                    $row->facility_name ?: '-'
                ),
            ];
        }));

        $displayRows = (clone $displaysBase)
            ->select([
                DB::raw('UNIX_TIMESTAMP(displays.updated_at) as ts'),
                'displays.id as display_id',
                'displays.connected',
                'displays.status',
                'displays.manufacturer',
                'displays.model',
                'displays.serial',
                'workstations.name as workstation_name',
                'workgroups.name as workgroup_name',
                'facilities.name as facility_name',
            ])
            ->whereNotNull('displays.updated_at')
            ->orderBy('displays.updated_at', 'desc')
            ->limit($oneThird)
            ->get();

        $events = $events->concat($displayRows->map(function ($row) {
            $connected = (int) $row->connected === 1;
            $failed = (int) $row->status === 2;
            $level = $connected ? ($failed ? 'WARN' : 'INFO') : 'WARN';
            $state = $connected ? 'CONNECTED' : 'DISCONNECTED';
            $status = $failed ? 'NOT_OK' : 'OK';
            $displayName = trim(($row->manufacturer ?? '') . ' ' . ($row->model ?? '') . ' (' . ($row->serial ?? '-') . ')');

            return [
                'timestamp' => (int) ($row->ts ?? 0),
                'level' => $level,
                'line' => sprintf(
                    '[DISPLAY] state=%s status=%s display=%d "%s" ws=%s wg=%s fac=%s',
                    $state,
                    $status,
                    (int) $row->display_id,
                    $displayName,
                    $row->workstation_name ?: '-',
                    $row->workgroup_name ?: '-',
                    $row->facility_name ?: '-'
                ),
            ];
        }));

        $historyRows = (clone $historyBase)
            ->select([
                'histories.id as history_id',
                'histories.time',
                'histories.result',
                'histories.name as history_name',
                'histories.header',
                'histories.levels',
                'histories.steps',
                'histories.measurements',
                'displays.id as display_id',
                'displays.manufacturer',
                'displays.model',
                'displays.serial',
                'workstations.name as workstation_name',
                'workgroups.name as workgroup_name',
                'facilities.name as facility_name',
            ])
            ->orderBy('histories.time', 'desc')
            ->orderBy('histories.id', 'desc')
            ->limit($oneQuarter)
            ->get();

        $events = $events->concat($historyRows->flatMap(function ($row) use ($makeSnippet) {
            $isOk = (int) $row->result === 2;
            $displayName = trim(($row->manufacturer ?? '') . ' ' . ($row->model ?? '') . ' (' . ($row->serial ?? '-') . ')');
            $payload = $row->measurements ?: ($row->steps ?: ($row->header ?: $row->levels));
            $payloadSnippet = $makeSnippet($payload, 160);

            return collect([
                [
                    'timestamp' => (int) $row->time,
                    'level' => $isOk ? 'INFO' : 'ERROR',
                    'line' => sprintf(
                        '[SYNC_%s] history=%d test="%s" display=%d "%s" ws=%s',
                        $isOk ? 'OK' : 'FAIL',
                        (int) $row->history_id,
                        $row->history_name ?: '-',
                        (int) $row->display_id,
                        $displayName,
                        $row->workstation_name ?: '-'
                    ),
                ],
                [
                    'timestamp' => (int) $row->time,
                    'level' => 'INFO',
                    'line' => sprintf(
                        '[RX_PAYLOAD] history=%d bytes~%d data=%s',
                        (int) $row->history_id,
                        strlen((string) $payload),
                        $payloadSnippet
                    ),
                ],
            ]);
        }));

        $jobRows = DB::table('jobs')
            ->select([
                DB::raw('UNIX_TIMESTAMP(created_at) as ts'),
                'id',
                'queue',
                'payload',
                'attempts',
            ])
            ->orderBy('id', 'desc')
            ->limit(max(4, (int) ceil($lineLimit / 8)))
            ->get();

        $events = $events->concat($jobRows->map(function ($row) use ($makeSnippet) {
            $payload = (string) ($row->payload ?? '');
            return [
                'timestamp' => (int) ($row->ts ?? 0),
                'level' => 'INFO',
                'line' => sprintf(
                    '[TX_PAYLOAD] job=%d queue=%s attempts=%d bytes=%d data=%s',
                    (int) $row->id,
                    $row->queue ?: '-',
                    (int) $row->attempts,
                    strlen($payload),
                    $makeSnippet($payload, 160)
                ),
            ];
        }));

        $failedJobRows = DB::table('failed_jobs')
            ->select([
                DB::raw('UNIX_TIMESTAMP(failed_at) as ts'),
                'id',
                'queue',
                'payload',
            ])
            ->orderBy('id', 'desc')
            ->limit(4)
            ->get();

        $events = $events->concat($failedJobRows->map(function ($row) use ($makeSnippet) {
            $payload = (string) ($row->payload ?? '');
            return [
                'timestamp' => (int) ($row->ts ?? 0),
                'level' => 'ERROR',
                'line' => sprintf(
                    '[TX_PAYLOAD_FAILED] failed_job=%d queue=%s bytes=%d data=%s',
                    (int) $row->id,
                    $row->queue ?: '-',
                    strlen($payload),
                    $makeSnippet($payload, 160)
                ),
            ];
        }));

        $notificationRows = DB::table('notifications')
            ->select([
                DB::raw('UNIX_TIMESTAMP(created_at) as ts'),
                'id',
                'type',
                'notifiable_id',
                'data',
            ])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $events = $events->concat($notificationRows->map(function ($row) use ($makeSnippet) {
            $payload = (string) ($row->data ?? '');
            return [
                'timestamp' => (int) ($row->ts ?? 0),
                'level' => 'INFO',
                'line' => sprintf(
                    '[TX_NOTIFY_PAYLOAD] notif=%s user=%d type=%s bytes=%d data=%s',
                    $row->id ?: '-',
                    (int) ($row->notifiable_id ?? 0),
                    $row->type ?: '-',
                    strlen($payload),
                    $makeSnippet($payload, 160)
                ),
            ];
        }));

        $latestNotification = $notificationRows->first();
        if ($latestNotification) {
            $latestPayload = (string) ($latestNotification->data ?? '');
            $events->push([
                'timestamp' => $now->timestamp,
                'level' => 'INFO',
                'line' => sprintf(
                    '[TX_PAYLOAD] source=notification notif=%s user=%d bytes=%d data=%s',
                    $latestNotification->id ?: '-',
                    (int) ($latestNotification->notifiable_id ?? 0),
                    strlen($latestPayload),
                    $makeSnippet($latestPayload, 160)
                ),
            ]);
        } elseif ($jobRows->isEmpty() && $failedJobRows->isEmpty()) {
            $events->push([
                'timestamp' => $now->timestamp,
                'level' => 'INFO',
                'line' => '[TX_PAYLOAD] source=queue data=no recent outbound payload snapshot',
            ]);
        }

        $events->push([
            'timestamp' => $now->timestamp,
            'level' => 'INFO',
            'line' => sprintf(
                '[TX_CHANNEL] jobs=%d failed_jobs=%d notifications=%d',
                $jobRows->count(),
                $failedJobRows->count(),
                $notificationRows->count()
            ),
        ]);

        $events = $events
            ->filter(fn($event) => (int) ($event['timestamp'] ?? 0) > 0)
            ->sort(function ($a, $b) {
                return ((int) ($b['timestamp'] ?? 0)) <=> ((int) ($a['timestamp'] ?? 0));
            })
            ->take($lineLimit)
            ->values()
            ->map(function ($event) {
                $timeLabel = \Carbon\Carbon::createFromTimestampUTC((int) $event['timestamp'])
                    ->setTimezone(config('app.timezone', 'UTC'))
                    ->format('Y-m-d H:i:s');

                return [
                    'timestamp' => (int) $event['timestamp'],
                    'level' => $event['level'],
                    'line' => sprintf('%s %-5s %s', $timeLabel, $event['level'], $event['line']),
                ];
            });

        return response()->json([
            'generated_at' => $now->toIso8601String(),
            'summary' => [
                'workstations_total' => $workstationsTotal,
                'workstations_online' => $workstationsOnline,
                'workstations_offline' => $workstationsOffline,
                'displays_total' => $displaysTotal,
                'displays_connected' => $displaysConnected,
                'displays_disconnected' => $displaysDisconnected,
                'displays_failed' => $displaysFailed,
                'sync_last_10m_total' => (int) ($throughput->total ?? 0),
                'sync_last_10m_ok' => (int) ($throughput->success_count ?? 0),
                'sync_last_10m_fail' => (int) ($throughput->failed_count ?? 0),
            ],
            'terminal_lines' => $events->pluck('line'),
        ]);
    }

    public function api_due_tasks(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        $role = $request->session()->get('role');

        if (!$user) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $requestedFacilityId = $request->get('facility_id');
        $facility_id = $role === 'super'
            ? ($requestedFacilityId !== null && $requestedFacilityId !== '' ? $requestedFacilityId : null)
            : $user->facility_id;
        $display_id = $request->get('display_id');
        $search = trim((string) $request->get('search', ''));
        $terms = $search !== '' ? preg_split('/\s+/', $search) : [];
        $limit = max(1, min((int) $request->get('limit', 10), 100));
        $page = max(1, (int) $request->get('page', 1));
        $offset = ($page - 1) * $limit;
        $nowTimestamp = now()->timestamp;
        $withoutTotal = $request->boolean('without_total');
        $cacheKey = null;

        if ($withoutTotal && $page === 1 && $limit <= 10 && !$display_id && $search === '') {
            $cacheKey = 'dashboard.api_due_tasks.v1.' . md5(implode('|', [
                $user_id,
                $role ?: 'guest',
                $facility_id ?: 'all',
                $limit,
            ]));

            if ($cachedPayload = \Illuminate\Support\Facades\Cache::get($cacheKey)) {
                return response()->json($cachedPayload);
            }
        }

        $taskFallbackDueExpression = "COALESCE(
            UNIX_TIMESTAMP(CONVERT_TZ(
                STR_TO_DATE(CONCAT(REPLACE(tasks.startdate, '.', '-'), ' ', LEFT(tasks.starttime, 5)), '%Y-%m-%d %H:%i'),
                COALESCE(facilities.timezone, 'UTC'),
                '+00:00'
            )),
            UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(REPLACE(tasks.startdate, '.', '-'), ' ', LEFT(tasks.starttime, 5)), '%Y-%m-%d %H:%i'))
        )";

        $taskBase = DB::table('tasks')
            ->join('displays', 'tasks.display_id', '=', 'displays.id')
            ->join('workstations', 'displays.workstation_id', '=', 'workstations.id')
            ->join('workgroups', 'workstations.workgroup_id', '=', 'workgroups.id')
            ->join('facilities', 'workgroups.facility_id', '=', 'facilities.id')
            ->join('task_types', 'tasks.type', '=', 'task_types.key')
            ->join('schedule_types', 'tasks.schtype', '=', 'schedule_types.client_id')
            ->where('tasks.deleted', 0)
            ->where('tasks.disabled', 0)
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->when($display_id, fn($q) => $q->where('tasks.display_id', $display_id))
            ->when(!empty($terms), function ($query) use ($terms) {
                $query->where(function ($query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where(function ($q) use ($term) {
                            $q->where('task_types.title', 'like', "%{$term}%")
                                ->orWhere('displays.manufacturer', 'like', "%{$term}%")
                                ->orWhere('displays.model', 'like', "%{$term}%")
                                ->orWhere('displays.serial', 'like', "%{$term}%")
                                ->orWhere('schedule_types.title', 'like', "%{$term}%")
                                ->orWhere('facilities.name', 'like', "%{$term}%")
                                ->orWhere('workstations.name', 'like', "%{$term}%")
                                ->orWhere('workgroups.name', 'like', "%{$term}%");
                        });
                    }
                });
            });

        $taskBase = $this->applyCompletedOneShotTaskVisibilityGuard($taskBase);

        $qaBase = DB::table('qa_tasks')
            ->join('displays', 'qa_tasks.display_id', '=', 'displays.id')
            ->join('workstations', 'displays.workstation_id', '=', 'workstations.id')
            ->join('workgroups', 'workstations.workgroup_id', '=', 'workgroups.id')
            ->join('facilities', 'workgroups.facility_id', '=', 'facilities.id')
            ->where('qa_tasks.deleted', 0)
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->when($display_id, fn($q) => $q->where('qa_tasks.display_id', $display_id))
            ->when(!empty($terms), function ($query) use ($terms) {
                $query->where(function ($query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where(function ($q) use ($term) {
                            $q->where('qa_tasks.name', 'like', "%{$term}%")
                                ->orWhere('displays.manufacturer', 'like', "%{$term}%")
                                ->orWhere('displays.model', 'like', "%{$term}%")
                                ->orWhere('displays.serial', 'like', "%{$term}%")
                                ->orWhere('facilities.name', 'like', "%{$term}%")
                                ->orWhere('workstations.name', 'like', "%{$term}%")
                                ->orWhere('workgroups.name', 'like', "%{$term}%");
                        });
                    }
                });
            });

        $qaBase = $this->applyQaTaskVisibilityGuard($qaBase);

        $taskDueFast = (clone $taskBase)
            ->where('tasks.nextrun', '>', 0)
            ->where('tasks.nextrun', '<=', $nowTimestamp)
            ->select([
                DB::raw("'task' as record_type"),
                'tasks.id', 'tasks.display_id',
                'tasks.type as task_type_key',
                'tasks.lastrun',
                'task_types.title as task_name',
                'schedule_types.title as schedule_name',
                DB::raw('NULL as startdate1'),
                DB::raw('NULL as startdate'),
                DB::raw('NULL as starttime'),
                DB::raw('tasks.nextrun as due_at'),
                DB::raw('tasks.nextrun as due_date_sort'),
                'displays.manufacturer', 'displays.model', 'displays.serial', 'displays.workstation_id',
                'workstations.id as ws_id', 'workstations.name as ws_name',
                'workgroups.id as wg_id', 'workgroups.name as wg_name',
                'facilities.id as fac_id',
                'facilities.name as fac_name',
                'facilities.timezone as fac_timezone',
                DB::raw('tasks.nextrun as computed_due_at'),
            ]);

        $taskDueFallback = (clone $taskBase)
            ->where('tasks.nextrun', 0)
            ->whereNotNull('tasks.startdate')
            ->whereNotNull('tasks.starttime')
            ->where('tasks.startdate', '!=', '')
            ->where('tasks.starttime', '!=', '')
            ->whereRaw($taskFallbackDueExpression . ' > 0')
            ->whereRaw($taskFallbackDueExpression . ' <= ?', [$nowTimestamp])
            ->select([
                DB::raw("'task' as record_type"),
                'tasks.id', 'tasks.display_id',
                'tasks.type as task_type_key',
                'tasks.lastrun',
                'task_types.title as task_name',
                'schedule_types.title as schedule_name',
                DB::raw('NULL as startdate1'),
                DB::raw('NULL as startdate'),
                DB::raw('NULL as starttime'),
                DB::raw($taskFallbackDueExpression . ' as due_at'),
                DB::raw($taskFallbackDueExpression . ' as due_date_sort'),
                'displays.manufacturer', 'displays.model', 'displays.serial', 'displays.workstation_id',
                'workstations.id as ws_id', 'workstations.name as ws_name',
                'workgroups.id as wg_id', 'workgroups.name as wg_name',
                'facilities.id as fac_id',
                'facilities.name as fac_name',
                'facilities.timezone as fac_timezone',
                DB::raw($taskFallbackDueExpression . ' as computed_due_at'),
            ]);

        $qaDueRows = (clone $qaBase)
            ->where('qa_tasks.nextdate', '>', 0)
            ->where('qa_tasks.nextdate', '<=', $nowTimestamp)
            ->select([
                DB::raw("'qa_task' as record_type"),
                'qa_tasks.id', 'qa_tasks.display_id',
                DB::raw('NULL as task_type_key'),
                DB::raw('0 as lastrun'),
                'qa_tasks.name as task_name',
                'qa_tasks.freq as schedule_name',
                DB::raw("'2019-08-27 00:00' as startdate1"),
                DB::raw('NULL as startdate'),
                DB::raw('NULL as starttime'),
                'qa_tasks.nextdate as due_at',
                'qa_tasks.nextdate as due_date_sort',
                'displays.manufacturer', 'displays.model', 'displays.serial', 'displays.workstation_id',
                'workstations.id as ws_id', 'workstations.name as ws_name',
                'workgroups.id as wg_id', 'workgroups.name as wg_name',
                'facilities.id as fac_id',
                'facilities.name as fac_name',
                'facilities.timezone as fac_timezone',
                DB::raw('qa_tasks.nextdate as computed_due_at'),
            ]);

        $taskDueRows = DB::query()
            ->fromSub($taskDueFast->unionAll($taskDueFallback), 'task_due_rows');

        $qaDueRowsScoped = DB::query()
            ->fromSub($qaDueRows, 'qa_due_rows');

        $allDueRows = (clone $taskDueRows)->unionAll(clone $qaDueRowsScoped);

        $taskCountFast = (clone $taskBase)
            ->where('tasks.nextrun', '>', 0)
            ->where('tasks.nextrun', '<=', $nowTimestamp)
            ->select([
                DB::raw("'task' as record_type"),
                'tasks.id',
                DB::raw('tasks.nextrun as computed_due_at'),
            ]);

        $taskCountFallback = (clone $taskBase)
            ->where('tasks.nextrun', 0)
            ->whereNotNull('tasks.startdate')
            ->whereNotNull('tasks.starttime')
            ->where('tasks.startdate', '!=', '')
            ->where('tasks.starttime', '!=', '')
            ->whereRaw($taskFallbackDueExpression . ' > 0')
            ->whereRaw($taskFallbackDueExpression . ' <= ?', [$nowTimestamp])
            ->select([
                DB::raw("'task' as record_type"),
                'tasks.id',
                DB::raw($taskFallbackDueExpression . ' as computed_due_at'),
            ]);

        $qaCountRows = (clone $qaBase)
            ->where('qa_tasks.nextdate', '>', 0)
            ->where('qa_tasks.nextdate', '<=', $nowTimestamp)
            ->select([
                DB::raw("'qa_task' as record_type"),
                'qa_tasks.id',
                DB::raw('qa_tasks.nextdate as computed_due_at'),
            ]);

        $allDueCountRows = DB::query()
            ->fromSub($taskCountFast->unionAll($taskCountFallback), 'task_due_count_rows')
            ->unionAll($qaCountRows);

        $total = $withoutTotal
            ? null
            : DB::query()->fromSub($allDueCountRows, 'all_due_count_rows')->count();

        $windowSize = $offset + $limit;
        if ($windowSize <= 1000) {
            $taskTopRows = (clone $taskDueRows)
                ->orderByDesc('computed_due_at')
                ->orderByDesc('id')
                ->limit($windowSize)
                ->get();

            $qaTopRows = (clone $qaDueRowsScoped)
                ->orderByDesc('computed_due_at')
                ->orderByDesc('id')
                ->limit($windowSize)
                ->get();

            $rows = $taskTopRows
                ->concat($qaTopRows)
                ->sort(function ($a, $b) {
                    $dueA = (int) ($a->computed_due_at ?? 0);
                    $dueB = (int) ($b->computed_due_at ?? 0);
                    if ($dueA === $dueB) {
                        return (int) ($b->id ?? 0) <=> (int) ($a->id ?? 0);
                    }
                    return $dueB <=> $dueA;
                })
                ->slice($offset, $limit)
                ->values();
        } else {
            $rows = DB::query()
                ->fromSub($allDueRows, 'all_due_rows')
                ->orderByDesc('computed_due_at')
                ->orderByDesc('id')
                ->offset($offset)
                ->limit($limit)
                ->get();
        }

        $formattedData = $rows->map(function ($record) {
            $mfg = $record->manufacturer ?? '';
            $mod = $record->model ?? '';
            $ser = $record->serial ?? '';
            $displayName = trim($mfg . ' ' . $mod . ' (' . $ser . ')');

            $dueAtTimestamp = (int) ($record->computed_due_at ?? 0);
            $dueAtFormatted = $this->formatRecordDueAt($record, $dueAtTimestamp);

            $isPast = false;
            $isToday = false;
            $diffForHumans = '-';
            if ($dueAtTimestamp) {
                $dueObj = \Carbon\Carbon::createFromTimestampUTC($dueAtTimestamp)
                    ->setTimezone($record->fac_timezone ?: config('app.timezone', 'UTC'));
                $isPast = $dueObj->isPast();
                $isToday = $dueObj->isToday();
                $diffForHumans = $isPast ? $dueObj->diffForHumans() : 'Not overdue';
            }

            $rescheduleTaskTypeKey = $this->resolveReschedulableTaskTypeKey(
                $record->task_type_key ?? null,
                $record->task_name ?? null
            );

            $dueColor = $isPast ? 'danger' : ($isToday ? 'warning' : 'success');
            $status = $isPast ? 'Overdue' : ($isToday ? 'Today' : 'Upcoming');

            return [
                'id'         => $record->id,
                'type'       => $record->record_type,
                'displayId'  => $record->display_id,
                'wsId'       => $record->ws_id ?? $record->workstation_id,
                'wgId'       => $record->wg_id,
                'facId'      => $record->fac_id,
                'displayName' => $displayName,
                'wsName'     => $record->ws_name ?? '-',
                'wgName'     => $record->wg_name ?? '-',
                'facName'    => $record->fac_name ?? '-',
                'task'       => $this->resolveTaskTypeLabel($record->task_type_key ?? null, $record->task_name ?? null, $record->record_type ?? null),
                'taskName'   => $this->resolveTaskTypeLabel($record->task_type_key ?? null, $record->task_name ?? null, $record->record_type ?? null),
                'schedule'   => $record->schedule_name ?? '-',
                'scheduleName' => $record->schedule_name ?? '-',
                'dueAt'      => $dueAtFormatted,
                'timestamp'  => $dueAtTimestamp,
                'isPast'     => $isPast,
                'isToday'    => $isToday,
                'overdue'    => $diffForHumans,
                'dueColor'   => $dueColor,
                'status'     => $status,
                'statusColor' => $dueColor,
                'reschedule' => ($rescheduleTaskTypeKey && $dueAtTimestamp)
                    ? [
                        'taskTypeKey' => $rescheduleTaskTypeKey,
                        'displayId' => (int) ($record->display_id ?? 0),
                        'facilityId' => (int) ($record->fac_id ?? 0),
                        'workgroupId' => (int) ($record->wg_id ?? 0),
                        'workstationId' => (int) ($record->ws_id ?? $record->workstation_id ?? 0),
                        'startDate' => $dueObj?->format('Y-m-d') ?? '',
                        'startTime' => $dueObj?->format('H:i') ?? '',
                        'dayOfMonth' => $dueObj ? max(1, (int) $dueObj->format('j')) : 1,
                        'monthNumber' => $dueObj ? max(1, (int) $dueObj->format('n')) : 1,
                        'dayOfWeek' => $dueObj ? max(1, (int) $dueObj->isoWeekday()) : 1,
                    ]
                    : null,
            ];
        })->values();

        $payload = [
            'data' => $formattedData,
            'total' => $total ?? $formattedData->count(),
        ];

        if ($cacheKey) {
            \Illuminate\Support\Facades\Cache::put($cacheKey, $payload, now()->addSeconds(20));
        }

        return response()->json($payload);
    }

    public function api_displays_failed(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        
        if (!$user) {
            return response()->json([]);
        }

        $facility_id = $user->facility_id;
        $limit = max(1, min((int) $request->get('limit', 10), 50));

        // Single JOIN query — no Eloquent eager loading chains
        $query = DB::table('displays')
            ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->join('display_preferences', function ($join) {
                $join->on('display_preferences.display_id', '=', 'displays.id')
                     ->where('display_preferences.name', '=', 'exclude')
                     ->where('display_preferences.value', '=', '0');
            })
            ->where('displays.status', 2)
            ->select([
                'displays.id',
                'displays.manufacturer',
                'displays.model',
                'displays.serial',
                'displays.errors',
                'displays.updated_at',
                'displays.workstation_id',
                'workstations.name as ws_name',
                'workstations.workgroup_id as wg_id',
                'workgroups.name as wg_name',
                'workgroups.facility_id as fac_id',
                'facilities.name as fac_name',
            ])
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->orderBy('displays.updated_at', 'desc')
            ->limit($limit)
            ->get();

        $formattedData = $query->map(function ($record) {
            $mfg = $record->manufacturer ?? '';
            $mod = $record->model ?? '';
            $ser = $record->serial ?? '';
            $displayName = trim($mfg . ' ' . $mod . ' (' . $ser . ')');

            $msg = 'Calibration Verification Failed';
            if ($record->errors) {
                $errors = json_decode($record->errors, true);
                if (is_array($errors) && count($errors) > 0) {
                    $latest = end($errors);
                    $msg = is_array($latest) ? ($latest['message'] ?? 'Display requires attention') : $latest;
                } else {
                    $msg = 'Calibration/QA Check Failed';
                }
            }

            return [
                'displayId' => $record->id,
                'wsId'      => $record->workstation_id,
                'wgId'      => $record->wg_id,
                'facId'     => $record->fac_id,
                'displayName' => $displayName,
                'wsName'    => $record->ws_name ?? '-',
                'wgName'    => $record->wg_name ?? '-',
                'facName'   => $record->fac_name ?? '-',
                'updatedAt' => $record->updated_at ? \Carbon\Carbon::parse($record->updated_at)->format('d M Y H:i') : '-',
                'errorMsg'  => $msg,
            ];
        });

        return response()->json($formattedData);
    }

    public function api_latest_performed(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        
        if (!$user) {
            return response()->json([]);
        }

        $role = $request->session()->get('role');
        $requestedFacilityId = $request->get('facility_id');
        $facility_id = $role === 'super'
            ? ($requestedFacilityId !== null && $requestedFacilityId !== '' ? $requestedFacilityId : null)
            : $user->facility_id;
        $limit = max(1, min((int) $request->get('limit', 10), 50));

        // Single JOIN query — no Eloquent eager loading chains
        $rows = DB::table('histories')
            ->join('displays', 'displays.id', '=', 'histories.display_id')
            ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->select([
                'histories.id as history_id',
                'histories.result',
                'histories.name',
                'histories.time',
                'displays.id as display_id',
                'displays.manufacturer',
                'displays.model',
                'workstations.name as ws_name',
                'workgroups.name as wg_name',
                'facilities.name as fac_name',
            ])
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->orderBy('histories.time', 'desc')
            ->orderBy('histories.id', 'desc')
            ->limit($limit)
            ->get();

        $formattedData = $rows->map(function ($history) {
            $displayName = trim(($history->manufacturer ?? '') . ' ' . ($history->model ?? '')) ?: 'Unknown Display';

            $timeObj = \Carbon\Carbon::createFromTimestamp($history->time);
            if ($timeObj->isToday()) {
                $timeFormatted = 'Today, ' . $timeObj->format('H:i');
            } elseif ($timeObj->isYesterday()) {
                $timeFormatted = 'Yesterday, ' . $timeObj->format('H:i');
            } else {
                $timeFormatted = $timeObj->format('Y-m-d H:i');
            }

            return [
                'historyId'     => $history->history_id,
                'displayId'     => $history->display_id,
                'result'        => $history->result == 2 ? 'ok' : 'fail',
                'name'          => $history->name ?? 'Calibration',
                'displayName'   => $displayName,
                'wsName'        => $history->ws_name ?? '-',
                'wgName'        => $history->wg_name ?? '-',
                'facName'       => $history->fac_name ?? '-',
                'timeFormatted' => $timeFormatted,
            ];
        });

        return response()->json($formattedData);
    }

    public function api_connection_watchlist(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        if (!$user) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $role = $request->session()->get('role');
        $requestedFacilityId = $request->get('facility_id');
        $facility_id = $role === 'super'
            ? ($requestedFacilityId !== null && $requestedFacilityId !== '' ? $requestedFacilityId : null)
            : $user->facility_id;
        $limit = max(1, min((int) $request->get('limit', 5), 20));
        $withoutTotal = $request->boolean('without_total');
        $staleThreshold = now()->subDays(7);

        $query = DB::table('workstations')
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->leftJoin(DB::raw('(
                SELECT workstation_id, COUNT(*) as cnt
                FROM displays
                GROUP BY workstation_id
            ) dc'), 'dc.workstation_id', '=', 'workstations.id')
            ->select([
                'workstations.id',
                'workstations.name',
                'workstations.last_connected',
                'workgroups.id as wg_id',
                'workgroups.name as wg_name',
                'facilities.id as fac_id',
                'facilities.name as fac_name',
                DB::raw('COALESCE(dc.cnt, 0) as displays_count'),
            ])
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->where(function ($q) use ($staleThreshold) {
                $q->whereNull('workstations.last_connected')
                    ->orWhere('workstations.last_connected', '<', $staleThreshold);
            });

        $rows = (clone $query)
            ->orderByRaw('CASE WHEN workstations.last_connected IS NULL THEN 0 ELSE 1 END')
            ->orderBy('workstations.last_connected', 'asc')
            ->orderBy('workstations.id', 'asc')
            ->limit($limit)
            ->get();

        $total = $withoutTotal ? $rows->count() : (clone $query)->count();

        $data = $rows->map(function ($row) {
            $lastConnected = $row->last_connected ? \Carbon\Carbon::parse($row->last_connected) : null;
            $daysSince = $lastConnected ? $lastConnected->diffInDays(\Carbon\Carbon::now()) : null;
            $lcColor = $lastConnected === null ? 'danger' : ($daysSince > 15 ? 'danger' : 'warning');

            return [
                'id' => $row->id,
                'name' => $row->name,
                'wgId' => $row->wg_id,
                'wgName' => $row->wg_name ?? '-',
                'facId' => $row->fac_id,
                'facName' => $row->fac_name ?? '-',
                'displaysCount' => (int) $row->displays_count,
                'lastConnected' => $lastConnected ? $lastConnected->format('d M Y H:i') : '-',
                'lastSeenRelative' => $lastConnected ? $lastConnected->diffForHumans() : 'No sync data',
                'lcColor' => $lcColor,
            ];
        })->values();

        return response()->json([
            'data' => $data,
            'total' => $total,
        ]);
    }

    // ─── API: DISPLAYS ───────────────────────────────────────────────────────
    public function api_displays(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        $role = $request->session()->get('role');
        if (!$user) return response()->json(['data' => [], 'total' => 0]);

        $requestedFacilityId = $request->get('facility_id');
        $facility_id = $role === 'super'
            ? ($requestedFacilityId !== null && $requestedFacilityId !== '' ? $requestedFacilityId : null)
            : $user->facility_id;
        $workstation_id = $request->get('workstation_id');
        $workgroup_id   = $request->get('workgroup_id');
        $display_ids    = collect(explode(',', (string) $request->get('display_ids', '')))->filter()->values()->all();
        $type    = $request->get('type');
        $status  = $request->get('status');
        $search  = $request->get('search', '');
        $sort    = $request->get('sort', 'updated_at');
        $order   = strtolower((string) $request->get('order', 'desc')) === 'asc' ? 'asc' : 'desc';
        $limit   = (int)$request->get('limit', 25);
        $page    = (int)$request->get('page', 1);
        $offset  = ($page - 1) * $limit;
        $cacheKey = null;

        if (
            $page === 1 &&
            $limit <= 10 &&
            in_array($type, ['failed', 'ok'], true) &&
            !$workstation_id &&
            !$workgroup_id &&
            empty($display_ids) &&
            ($status === null || $status === '') &&
            trim((string) $search) === ''
        ) {
            $cacheKey = 'dashboard.api_displays.v1.' . md5(implode('|', [
                $user_id,
                $role ?: 'guest',
                $facility_id ?: 'all',
                $type,
                $sort,
                $order,
                $limit,
            ]));

            if ($cachedPayload = \Illuminate\Support\Facades\Cache::get($cacheKey)) {
                return response()->json($cachedPayload);
            }
        }

        $baseQuery = DB::table('displays')
            ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
            ->join('workgroups',   'workgroups.id',   '=', 'workstations.workgroup_id')
            ->join('facilities',   'facilities.id',   '=', 'workgroups.facility_id')
            ->leftJoin('display_preferences as excluded_pref', function ($join) {
                $join->on('excluded_pref.display_id', '=', 'displays.id')
                    ->where('excluded_pref.name', '=', 'exclude')
                    ->where('excluded_pref.value', '=', '1');
            })
            ->whereNull('excluded_pref.id')
            ->when($facility_id,    fn($q) => $q->where('workgroups.facility_id',  $facility_id))
            ->when($workstation_id, fn($q) => $q->where('displays.workstation_id', $workstation_id))
            ->when($workgroup_id,   fn($q) => $q->where('workstations.workgroup_id', $workgroup_id))
            ->when(!empty($display_ids), fn($q) => $q->whereIn('displays.id', $display_ids))
            ->when($status !== null && $status !== '', fn($q) => $q->where('displays.status', $status))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('displays.manufacturer', 'like', "%$search%")
                   ->orWhere('displays.model',      'like', "%$search%")
                   ->orWhere('displays.serial',     'like', "%$search%")
                   ->orWhere('workstations.name',   'like', "%$search%")
                   ->orWhere('workgroups.name',     'like', "%$search%")
                   ->orWhere('facilities.name',     'like', "%$search%");
            }));

        $baseSelect = [
            'displays.id', 'displays.manufacturer', 'displays.model', 'displays.serial',
            'displays.status', 'displays.connected', 'displays.workstation_id',
            'displays.created_at', 'displays.updated_at', 'displays.errors',
            'workstations.name as ws_name', 'workstations.workgroup_id as wg_id', 'workstations.last_connected as ws_last_connected',
            'workgroups.name as wg_name', 'workgroups.facility_id as fac_id',
            'workgroups.address as wg_address', 'workgroups.city as wg_city', 'workgroups.state as wg_state',
            'facilities.name as fac_name', 'facilities.timezone as fac_timezone',
        ];

        $requiresGlobalFailureMode = in_array($type, ['failed', 'ok'], true);

        if (!$requiresGlobalFailureMode) {
            $latestHistorySubquery = DB::table('histories')
                ->selectRaw('display_id, MAX(updated_at) as latest_history_at')
                ->whereNull('deleted_at')
                ->groupBy('display_id');

            $fastRowsQuery = (clone $baseQuery)
                ->leftJoinSub($latestHistorySubquery, 'latest_history', function ($join) {
                    $join->on('latest_history.display_id', '=', 'displays.id');
                })
                ->select(array_merge($baseSelect, ['latest_history.latest_history_at']));

            switch ($sort) {
                case 'id':
                    $fastRowsQuery->orderBy('displays.id', $order);
                    break;
                case 'manufacturer':
                case 'display_name':
                    $fastRowsQuery->orderByRaw(
                        "LOWER(CONCAT(COALESCE(displays.manufacturer,''), ' ', COALESCE(displays.model,''), ' ', COALESCE(displays.serial,''))) {$order}"
                    );
                    break;
                case 'status':
                    $fastRowsQuery->orderBy('displays.status', $order);
                    break;
                case 'updated_at':
                default:
                    $fastRowsQuery->orderByRaw(
                        "COALESCE(latest_history.latest_history_at, displays.updated_at, workstations.last_connected, displays.created_at) {$order}"
                    );
                    break;
            }

            $total = (clone $baseQuery)->count();
            $rows = $fastRowsQuery
                ->orderBy('displays.id', $order === 'asc' ? 'asc' : 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get()
                ->map(function ($row) {
                    $row->latest_history_at = $row->latest_history_at ?? null;
                    $row->latest_hours_at = null;
                    $row->latest_hours_duration = null;
                    $row->latest_hours_synced_at = null;
                    $row->latest_activity_at = $row->latest_history_at ?: ($row->updated_at ?: ($row->ws_last_connected ?: $row->created_at));
                    $row->latest_activity_source = $row->latest_history_at ? 'history' : ($row->updated_at ? 'sync' : ($row->ws_last_connected ? 'workstation' : 'created'));
                    $row->latest_failed_history_name = null;
                    $row->latest_failed_history_at = null;
                    $row->latest_failed_history_time = null;
                    $row->has_unresolved_failure = false;
                    $row->unresolved_tests_count = 0;

                    return $row;
                });

            $rows = collect($this->applyDashboardFailedDisplayMode(
                $rows->map(fn ($row) => (array) $row)->all()
            ))->map(function (array $row) {
                return (object) $row;
            })->values();
        } else {
            $rows = (clone $baseQuery)
                ->select($baseSelect)
                ->get();

            $displayIds = $rows->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->unique()
                ->values();

            $historyActivityMap = $displayIds->isEmpty()
                ? []
                : DB::table('histories')
                    ->selectRaw('display_id, MAX(updated_at) as latest_history_at')
                    ->whereNull('deleted_at')
                    ->whereIn('display_id', $displayIds->all())
                    ->groupBy('display_id')
                    ->pluck('latest_history_at', 'display_id')
                    ->map(fn ($value) => $value ? (string) $value : null)
                    ->all();

            $rows = $rows->map(function ($row) use ($historyActivityMap) {
                $historyAt = $historyActivityMap[$row->id] ?? null;
                $displayUpdatedAt = $row->updated_at ? (string) $row->updated_at : null;
                $workstationLastConnected = $row->ws_last_connected ? (string) $row->ws_last_connected : null;
                $createdAt = $row->created_at ? (string) $row->created_at : null;

                $activityCandidates = collect([
                    ['source' => 'history', 'value' => $historyAt],
                    ['source' => 'sync', 'value' => $displayUpdatedAt],
                    ['source' => 'workstation', 'value' => $workstationLastConnected],
                ])->filter(fn ($item) => !empty($item['value']))
                    ->sortByDesc('value')
                    ->values();

                $latestActivity = $activityCandidates->first();
                $latestActivityAt = $latestActivity['value'] ?? $createdAt;
                $latestActivitySource = $latestActivity['source'] ?? ($createdAt ? 'created' : 'none');

                $row->latest_history_at = $historyAt;
                $row->latest_hours_at = null;
                $row->latest_hours_duration = null;
                $row->latest_hours_synced_at = null;
                $row->latest_activity_at = $latestActivityAt;
                $row->latest_activity_source = $latestActivitySource;
                $row->latest_failed_history_name = null;
                $row->latest_failed_history_at = null;
                $row->latest_failed_history_time = null;
                $row->has_unresolved_failure = false;
                $row->unresolved_tests_count = 0;

                return $row;
            })->values();

            $rows = collect($this->applyDashboardFailedDisplayMode(
                $rows->map(fn ($row) => (array) $row)->all()
            ))->map(function (array $row) {
                return (object) $row;
            })->values();

            if ($type === 'failed') {
                $rows = $rows->filter(fn ($row) => !empty($row->has_unresolved_failure))->values();
            } elseif ($type === 'ok') {
                $rows = $rows->filter(fn ($row) => empty($row->has_unresolved_failure))->values();
            }

            switch ($sort) {
                case 'id':
                    $rows = $rows->sortBy('id', SORT_NATURAL, $order === 'desc')->values();
                    break;
                case 'manufacturer':
                    $rows = $rows->sortBy(fn ($row) => mb_strtolower(trim(($row->manufacturer ?? '') . ' ' . ($row->model ?? '') . ' ' . ($row->serial ?? ''))), SORT_NATURAL, $order === 'desc')->values();
                    break;
                case 'status':
                    $rows = $rows->sortBy('status', SORT_NATURAL, $order === 'desc')->values();
                    break;
                case 'display_name':
                    $rows = $rows->sortBy(fn ($row) => mb_strtolower(trim(($row->manufacturer ?? '') . ' ' . ($row->model ?? '') . ' ' . ($row->serial ?? ''))), SORT_NATURAL, $order === 'desc')->values();
                    break;
                case 'updated_at':
                default:
                    $rows = $rows->sortBy(fn ($row) => $row->latest_failed_history_time ?: ($row->updated_at ?? ''), SORT_NATURAL, $order === 'desc')->values();
                    break;
            }

            $total = $rows->count();
            $rows = $rows->slice($offset, $limit)->values();
        }

        $failedHistorySummaryMap = [];
        $pageDisplayIds = $rows->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $pageHoursSummaryMap = [];

        if ($pageDisplayIds->isNotEmpty()) {
            $expectedDisplayCount = $pageDisplayIds->count();

            foreach (
                DB::table('display_hours')
                    ->select(['id', 'display_id', 'start', 'duration', 'updated_at'])
                    ->whereIn('display_id', $pageDisplayIds->all())
                    ->orderBy('display_id')
                    ->orderByDesc('start')
                    ->orderByDesc('id')
                    ->cursor() as $row
            ) {
                $displayId = (int) $row->display_id;

                if (isset($pageHoursSummaryMap[$displayId])) {
                    continue;
                }

                $pageHoursSummaryMap[$displayId] = [
                    'latest_hours_at' => $row->start ? (string) $row->start : null,
                    'latest_hours_duration' => $row->duration !== null ? (float) $row->duration : null,
                    'latest_hours_synced_at' => $row->updated_at ? (string) $row->updated_at : null,
                ];

                if (count($pageHoursSummaryMap) >= $expectedDisplayCount) {
                    break;
                }
            }
        }

        if ($pageDisplayIds->isNotEmpty()) {
            $failedHistories = \App\Models\History::query()
                ->whereNull('deleted_at')
                ->where('result', 3)
                ->whereIn('display_id', $pageDisplayIds->all())
                ->orderBy('display_id')
                ->orderByDesc('time')
                ->orderByDesc('id')
                ->get()
                ->groupBy('display_id');

            foreach ($failedHistories as $displayId => $group) {
                $latestFailedHistory = $group->first();
                $latestFailedCheckText = null;

                foreach (($latestFailedHistory->steps ?? []) as $step) {
                    foreach (($step['scores'] ?? []) as $score) {
                        if (($score['answer'] ?? null) != 0) {
                            continue;
                        }

                        $name = trim(strip_tags((string) ($score['name'] ?? '')));
                        $measured = trim(html_entity_decode(strip_tags((string) ($score['measured'] ?? ''))));

                        if ($name && $measured && $measured !== '-') {
                            $latestFailedCheckText = $name . ': ' . $measured;
                        } elseif ($name) {
                            $latestFailedCheckText = $name;
                        }

                        break 2;
                    }
                }

                $failedHistorySummaryMap[(int) $displayId] = [
                    'latest_failed_history_name' => trim((string) ($latestFailedHistory->name ?? '')) ?: null,
                    'latest_failed_check_text' => $latestFailedCheckText,
                ];
            }
        }

        $formatHours = function ($value) {
            if ($value === null || $value === '') {
                return '-';
            }

            $hours = (float) $value;
            $formatted = floor($hours) == $hours
                ? number_format($hours, 0)
                : number_format($hours, 1);

            return $formatted . ' h';
        };

        $extractLatestErrorText = function ($errors): string {
            if (!is_array($errors) || count($errors) === 0) {
                return '';
            }

            $latest = end($errors);

            if (is_string($latest)) {
                return trim($latest);
            }

            if (is_array($latest)) {
                return trim((string) ($latest['text'] ?? $latest['error'] ?? $latest['message'] ?? $latest['name'] ?? ''));
            }

            return '';
        };

        $data = $rows->map(function ($r) use ($role, $formatHours, $failedHistorySummaryMap, $extractLatestErrorText, $pageHoursSummaryMap) {
            $parts = array_filter([$r->wg_address, $r->wg_city, $r->wg_state]);
            $errors = json_decode($r->errors ?? '[]', true);
            $hoursMeta = $pageHoursSummaryMap[(int) $r->id] ?? [
                'latest_hours_at' => null,
                'latest_hours_duration' => null,
                'latest_hours_synced_at' => null,
            ];
            $hoursAt = $hoursMeta['latest_hours_at'] ?? null;
            $hoursSyncedAt = $hoursMeta['latest_hours_synced_at'] ?? null;
            $hoursDuration = $hoursMeta['latest_hours_duration'] ?? null;
            $syncAt = $hoursSyncedAt ?: $r->updated_at ?: $r->ws_last_connected;
            $displayTimezone = $r->fac_timezone ?: config('app.timezone', 'UTC');
            $syncFormatted = $syncAt ? \Carbon\Carbon::parse($syncAt)->setTimezone($displayTimezone)->format('d M Y H:i') : '-';
            $createdFormatted = $r->created_at ? \Carbon\Carbon::parse($r->created_at)->setTimezone($displayTimezone)->format('d M Y H:i') : '-';
            $failedSummary = $failedHistorySummaryMap[(int) $r->id] ?? [];
            $issueText = $extractLatestErrorText($errors);
            $failedCheckText = trim((string) ($failedSummary['latest_failed_check_text'] ?? ''));
            $failedHistoryText = trim((string) ($failedSummary['latest_failed_history_name'] ?? ($r->latest_failed_history_name ?: '')));
            $unresolvedTestsCount = (int) ($failedSummary['unresolved_tests_count'] ?? ($r->unresolved_tests_count ?? 0));
            $healthy = (int) $r->status === 1 && empty($r->has_unresolved_failure);

            if ($healthy) {
                $attentionText = __('No active alert');
                $attentionMode = 'healthy';
            } elseif (!empty($r->has_unresolved_failure) && $failedHistoryText !== '') {
                $attentionText = __('Unresolved failed test: :test', ['test' => $failedHistoryText]);
                if ($unresolvedTestsCount > 1) {
                    $attentionText .= ' ' . __('(+:count more)', ['count' => $unresolvedTestsCount - 1]);
                }
                $attentionMode = 'failed_history';
            } elseif ($issueText !== '') {
                $attentionText = $issueText;
                $attentionMode = 'live';
            } elseif ($failedCheckText !== '') {
                $attentionText = $failedCheckText;
                $attentionMode = 'failed_check';
            } elseif ($failedHistoryText !== '') {
                $attentionText = $failedHistoryText;
                $attentionMode = 'failed_history';
            } else {
                $attentionText = __('No alert detail');
                $attentionMode = 'placeholder';
            }

            return [
                'id'          => $r->id,
                'displayName' => trim(($r->manufacturer ?? '') . ' ' . ($r->model ?? '')) . ' (' . ($r->serial ?? '') . ')',
                'wsName'      => $r->ws_name  ?? '-',
                'wgName'      => $r->wg_name  ?? '-',
                'facName'     => $r->fac_name ?? '-',
                'wsId'        => $r->workstation_id,
                'wgId'        => $r->wg_id,
                'facId'       => $r->fac_id,
                'status'      => $r->status,
                'connected'   => (bool) $r->connected,
                'location'    => count($parts) ? implode(', ', $parts) : '-',
                'updatedAt'   => $syncFormatted,
                'createdAt'   => $createdFormatted,
                'latestHistoryAt' => $r->latest_history_at ? \Carbon\Carbon::parse($r->latest_history_at)->setTimezone($displayTimezone)->format('d M Y H:i') : '-',
                'latestHoursAt' => $hoursAt ? \Carbon\Carbon::parse($hoursAt)->setTimezone($displayTimezone)->format('d M Y H:i') : '-',
                'latestHoursSyncedAt' => $hoursSyncedAt ? \Carbon\Carbon::parse($hoursSyncedAt)->setTimezone($displayTimezone)->format('d M Y H:i') : '-',
                'latestHoursDuration' => $hoursDuration !== null ? (float) $hoursDuration : null,
                'latestHoursFormatted' => $formatHours($hoursDuration),
                'latestActivityMode' => 'sync',
                'latestActivitySource' => 'sync',
                'attentionText' => $attentionText,
                'attentionMode' => $attentionMode,
                'latestFailedHistoryName' => $failedSummary['latest_failed_history_name'] ?? ($r->latest_failed_history_name ?: null),
                'latestFailedCheckText' => $failedSummary['latest_failed_check_text'] ?? null,
                'unresolvedTestsCount' => $unresolvedTestsCount,
                'latestFailedHistoryAt' => $r->latest_failed_history_at
                    ? \Carbon\Carbon::parse($r->latest_failed_history_at)->setTimezone($displayTimezone)->format('d M Y H:i')
                    : ($r->latest_failed_history_time
                        ? \Carbon\Carbon::createFromTimestamp($r->latest_failed_history_time, 'UTC')->setTimezone($displayTimezone)->format('d M Y H:i')
                        : '-'),
                'errors'      => is_array($errors) ? $errors : [],
                'canManage'   => in_array($role, ['super', 'admin'], true),
                'canDelete'   => in_array($role, ['super', 'admin'], true),
            ];
        });

        $payload = ['data' => $data, 'total' => $total];

        if ($cacheKey) {
            \Illuminate\Support\Facades\Cache::put($cacheKey, $payload, now()->addSeconds(20));
        }

        return response()->json($payload);
    }

    // ─── API: WORKSTATIONS ───────────────────────────────────────────────────
    public function api_workstations(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        $role = $request->session()->get('role');
        if (!$user) return response()->json(['data' => [], 'total' => 0]);

        $userRole = $request->session()->get('role');
        $requestedFacilityId = $request->get('facility_id');
        $facility_id = $userRole === 'super'
            ? ($requestedFacilityId !== null && $requestedFacilityId !== '' ? $requestedFacilityId : null)
            : $user->facility_id;
        $workgroup_id = $request->get('workgroup_id');
        $type = $request->get('type');
        $staleOnly = $request->boolean('stale');
        $staleThreshold = now()->subDays(7);
        $search = $request->get('search', '');
        $sort = (string) $request->get('sort', ($staleOnly ? 'lastConnected' : 'name'));
        $order = strtolower((string) $request->get('order', ($staleOnly ? 'asc' : 'asc'))) === 'desc' ? 'desc' : 'asc';
        $limit  = (int)$request->get('limit', 25);
        $page   = (int)$request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $query = DB::table('workstations')
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->leftJoin(DB::raw('(
                SELECT
                    workstation_id,
                    COUNT(*) as cnt,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as ok_cnt,
                    SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as failed_cnt
                FROM displays
                GROUP BY workstation_id
            ) dc'),
                'dc.workstation_id', '=', 'workstations.id')
            ->select([
                'workstations.id', 'workstations.name', 'workstations.last_connected', 'workstations.sleep_time', 'workstations.workgroup_id as wg_id',
                'workgroups.name as wg_name', 'workgroups.facility_id as fac_id',
                'facilities.name as fac_name',
                DB::raw('COALESCE(dc.cnt, 0) as displays_count'),
                DB::raw('COALESCE(dc.ok_cnt, 0) as ok_displays_count'),
                DB::raw('COALESCE(dc.failed_cnt, 0) as failed_displays_count'),
            ])
            ->when($facility_id,  fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->when($workgroup_id, fn($q) => $q->where('workstations.workgroup_id', $workgroup_id))
            ->when($type === 'ok', fn($q) => $q->whereRaw('COALESCE(dc.failed_cnt, 0) = 0 AND COALESCE(dc.ok_cnt, 0) = COALESCE(dc.cnt, 0) AND COALESCE(dc.cnt, 0) > 0'))
            ->when($type === 'failed', fn($q) => $q->whereRaw('COALESCE(dc.failed_cnt, 0) > 0'))
            ->when($staleOnly, fn($q) => $q->where(function ($q2) use ($staleThreshold) {
                $q2->whereNull('workstations.last_connected')
                    ->orWhere('workstations.last_connected', '<', $staleThreshold);
            }))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('workstations.name', 'like', "%$search%")
                   ->orWhere('workgroups.name', 'like', "%$search%")
                   ->orWhere('facilities.name', 'like', "%$search%");
            }));

        $total = $query->count();
        $rowsQuery = (clone $query);
        $sortableMap = [
            'name' => 'workstations.name',
            'wgName' => 'workgroups.name',
            'facName' => 'facilities.name',
            'sleepTime' => 'workstations.sleep_time',
            'displaysCount' => DB::raw('COALESCE(dc.cnt, 0)'),
            'lastConnected' => 'workstations.last_connected',
        ];
        $sortColumn = $sortableMap[$sort] ?? 'workstations.name';

        if ($sort === 'lastConnected') {
            $nullRankExpr = $staleOnly
                ? "CASE WHEN workstations.last_connected IS NULL THEN 0 ELSE 1 END ASC"
                : "CASE WHEN workstations.last_connected IS NULL THEN 1 ELSE 0 END ASC";
            $rowsQuery->orderByRaw($nullRankExpr);
        }
        $rowsQuery
            ->orderBy($sortColumn, $order)
            ->orderBy('workstations.id', 'asc');

        $rows = $rowsQuery->offset($offset)->limit($limit)->get();

        $attentionCounts = [];
        $pageWorkstationIds = $rows->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (!empty($pageWorkstationIds)) {
            $attentionDisplayRows = DB::table('displays')
                ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
                ->leftJoin('display_preferences as excluded_pref', function ($join) {
                    $join->on('excluded_pref.display_id', '=', 'displays.id')
                        ->where('excluded_pref.name', '=', 'exclude')
                        ->where('excluded_pref.value', '=', '1');
                })
                ->whereNull('excluded_pref.id')
                ->whereIn('workstations.id', $pageWorkstationIds)
                ->select([
                    'displays.id',
                    'displays.workstation_id',
                    'displays.status',
                    'displays.errors',
                    'displays.updated_at',
                ])
                ->get()
                ->map(fn ($row) => (array) $row)
                ->all();

            $attentionDisplayRows = $this->applyDashboardFailedDisplayMode($attentionDisplayRows);

            $attentionCounts = collect($attentionDisplayRows)
                ->filter(fn ($row) => !empty($row['has_unresolved_failure']))
                ->groupBy(fn ($row) => (int) ($row['workstation_id'] ?? 0))
                ->map(fn ($group) => $group->count())
                ->all();
        }

        $data = $rows->map(function($r) use ($role, $attentionCounts) {
            $lc = $r->last_connected ? \Carbon\Carbon::parse($r->last_connected, config('app.timezone', 'UTC')) : null;
            $lcFormatted = $lc ? $lc->format('d M Y H:i') : '-';
            $daysSince = $lc ? $lc->diffInDays(\Carbon\Carbon::now()) : 999;
            $lcColor = $daysSince > 15 ? 'danger' : ($daysSince > 7 ? 'warning' : 'success');
            $attentionCount = (int) ($attentionCounts[(int) $r->id] ?? 0);
            return [
                'id'            => $r->id,
                'name'          => $r->name,
                'wgId'          => $r->wg_id,
                'wgName'        => $r->wg_name  ?? '-',
                'facId'         => $r->fac_id,
                'facName'       => $r->fac_name ?? '-',
                'sleepTime'     => $r->sleep_time ?: 'Off',
                'lastConnected' => $lcFormatted,
                'lastConnectedAt' => $lc ? $lc->copy()->utc()->toIso8601String() : null,
                'lastSeenRelative' => $lc ? $lc->diffForHumans() : 'No sync data',
                'lcColor'       => $lcColor,
                'displaysCount' => (int)$r->displays_count,
                'okDisplaysCount' => (int)$r->ok_displays_count,
                'failedDisplaysCount' => (int)$r->failed_displays_count,
                'attentionCount' => $attentionCount,
                'displayHealth' => (int)$r->failed_displays_count > 0
                    ? 'failed'
                    : (((int)$r->displays_count > 0) && ((int)$r->ok_displays_count === (int)$r->displays_count) ? 'ok' : 'unknown'),
                'canManage'     => in_array($role, ['super', 'admin'], true),
                'canDelete'     => in_array($role, ['super', 'admin'], true),
            ];
        });

        return response()->json(['data' => $data, 'total' => $total]);
    }

    // ─── API: WORKGROUPS ─────────────────────────────────────────────────────
    public function api_workgroups(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        $role = $request->session()->get('role');
        if (!$user) return response()->json(['data' => [], 'total' => 0]);

        $userRole = $request->session()->get('role');
        $requestedFacilityId = $request->get('facility_id');
        $facility_id = $userRole === 'super'
            ? ($requestedFacilityId !== null && $requestedFacilityId !== '' ? $requestedFacilityId : null)
            : $user->facility_id;
        $type = $request->get('type');
        $search = $request->get('search', '');
        $sort = (string) $request->get('sort', 'name');
        $order = strtolower((string) $request->get('order', 'asc')) === 'desc' ? 'desc' : 'asc';
        $limit  = (int)$request->get('limit', 25);
        $page   = (int)$request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $query = DB::table('workgroups')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->leftJoin(DB::raw('(SELECT workgroup_id, COUNT(*) as cnt FROM workstations GROUP BY workgroup_id) wsc'),
                'wsc.workgroup_id', '=', 'workgroups.id')
            ->leftJoin(DB::raw('(
                SELECT
                    workgroups.id as wg_id,
                    COUNT(displays.id) as cnt,
                    SUM(CASE WHEN displays.status = 1 THEN 1 ELSE 0 END) as ok_cnt,
                    SUM(CASE WHEN displays.status = 2 THEN 1 ELSE 0 END) as failed_cnt
                FROM displays
                JOIN workstations ON workstations.id = displays.workstation_id
                JOIN workgroups ON workgroups.id = workstations.workgroup_id
                GROUP BY workgroups.id
            ) dsc'),
                'dsc.wg_id', '=', 'workgroups.id')
            ->select([
                'workgroups.id', 'workgroups.name', 'workgroups.address', 'workgroups.phone', 'workgroups.facility_id as fac_id',
                'facilities.name as fac_name',
                DB::raw('COALESCE(wsc.cnt, 0) as workstations_count'),
                DB::raw('COALESCE(dsc.cnt, 0) as displays_count'),
                DB::raw('COALESCE(dsc.ok_cnt, 0) as ok_displays_count'),
                DB::raw('COALESCE(dsc.failed_cnt, 0) as failed_displays_count'),
            ])
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->when($type === 'ok', fn($q) => $q->whereRaw('COALESCE(dsc.failed_cnt, 0) = 0 AND COALESCE(dsc.ok_cnt, 0) = COALESCE(dsc.cnt, 0) AND COALESCE(dsc.cnt, 0) > 0'))
            ->when($type === 'failed', fn($q) => $q->whereRaw('COALESCE(dsc.failed_cnt, 0) > 0'))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('workgroups.name', 'like', "%$search%")
                   ->orWhere('facilities.name', 'like', "%$search%");
            }));

        $total = $query->count();
        $sortableMap = [
            'name' => 'workgroups.name',
            'address' => 'workgroups.address',
            'phone' => 'workgroups.phone',
            'facName' => 'facilities.name',
            'workstationsCount' => DB::raw('COALESCE(wsc.cnt, 0)'),
            'displaysCount' => DB::raw('COALESCE(dsc.cnt, 0)'),
        ];
        $sortColumn = $sortableMap[$sort] ?? 'workgroups.name';

        $rows  = (clone $query)
            ->orderBy($sortColumn, $order)
            ->orderBy('workgroups.id', 'asc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $data = $rows->map(fn($r) => [
            'id'                => $r->id,
            'name'              => $r->name,
            'address'           => $r->address           ?? '-',
            'phone'             => $r->phone             ?? '-',
            'facId'             => $r->fac_id,
            'facName'           => $r->fac_name          ?? '-',
            'workstationsCount' => (int)$r->workstations_count,
            'displaysCount'     => (int)$r->displays_count,
            'okDisplaysCount'   => (int)$r->ok_displays_count,
            'failedDisplaysCount' => (int)$r->failed_displays_count,
            'displayHealth'     => (int)$r->failed_displays_count > 0
                ? 'failed'
                : (((int)$r->displays_count > 0) && ((int)$r->ok_displays_count === (int)$r->displays_count) ? 'ok' : 'unknown'),
            'canManage'         => in_array($role, ['super', 'admin'], true),
            'canDelete'         => in_array($role, ['super', 'admin'], true),
        ]);

        return response()->json(['data' => $data, 'total' => $total]);
    }

    // ─── API: HISTORIES ──────────────────────────────────────────────────────
    public function api_histories(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        if (!$user) return response()->json(['data' => [], 'total' => 0]);

        $role = $request->session()->get('role');
        $requestedFacilityId = $request->get('facility_id');
        $facility_id = $role === 'super'
            ? ($requestedFacilityId !== null && $requestedFacilityId !== '' ? $requestedFacilityId : null)
            : $user->facility_id;
        $workgroup_id   = $request->get('workgroup_id');
        $workstation_id = $request->get('workstation_id');
        $display_id     = $request->get('display_id');
        $display_ids    = collect(explode(',', (string) $request->get('display_ids', '')))->filter()->values()->all();
        $search = $request->get('search', '');
        $sort = (string) $request->get('sort', 'time');
        $order = strtolower((string) $request->get('order', 'desc')) === 'asc' ? 'asc' : 'desc';
        $limit  = max(1, min((int)$request->get('limit', 25), 100));
        $page   = max(1, (int)$request->get('page', 1));
        $offset = ($page - 1) * $limit;

        $query = DB::table('histories')
            ->join('displays',    'displays.id',    '=', 'histories.display_id')
            ->join('workstations','workstations.id','=', 'displays.workstation_id')
            ->join('workgroups',  'workgroups.id',  '=', 'workstations.workgroup_id')
            ->join('facilities',  'facilities.id',  '=', 'workgroups.facility_id')
            ->select([
                'histories.id', 'histories.result', 'histories.name', 'histories.time', 'histories.regulation',
                'displays.manufacturer', 'displays.model', 'displays.serial',
                'histories.display_id',
                'workstations.id as ws_id',
                'workgroups.id as wg_id',
                'workstations.name as ws_name',
                'workgroups.name as wg_name',
                'facilities.name as fac_name',
            ])
            ->when($facility_id,    fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->when($workgroup_id,   fn($q) => $q->where('workstations.workgroup_id', $workgroup_id))
            ->when($workstation_id, fn($q) => $q->where('displays.workstation_id', $workstation_id))
            ->when($display_id,     fn($q) => $q->where('histories.display_id', $display_id))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('histories.name',        'like', "%$search%")
                   ->orWhere('histories.regulation','like', "%$search%")
                   ->orWhere('displays.manufacturer', 'like', "%$search%")
                   ->orWhere('displays.model',      'like', "%$search%")
                   ->orWhere('displays.serial',     'like', "%$search%")
                   ->orWhere('workstations.name',   'like', "%$search%")
                   ->orWhere('workgroups.name',     'like', "%$search%")
                   ->orWhere('facilities.name',     'like', "%$search%");
            }));

        $total = $query->count();
        $rowsQuery = clone $query;
        switch ($sort) {
            case 'name':
                $rowsQuery->orderBy('histories.name', $order);
                break;
            case 'pattern':
                $rowsQuery->orderBy('histories.regulation', $order);
                break;
            case 'display_name':
                $rowsQuery->orderBy('displays.manufacturer', $order)
                    ->orderBy('displays.model', $order)
                    ->orderBy('displays.serial', $order);
                break;
            case 'ws_name':
                $rowsQuery->orderBy('workstations.name', $order);
                break;
            case 'wg_name':
                $rowsQuery->orderBy('workgroups.name', $order);
                break;
            case 'result':
                $rowsQuery->orderBy('histories.result', $order);
                break;
            case 'time':
            default:
                $rowsQuery->orderBy('histories.time', $order);
                break;
        }
        $rows = $rowsQuery->orderBy('histories.id', 'desc')->offset($offset)->limit($limit)->get();

        $data = $rows->map(fn($r) => [
            'id'          => $r->id,
            'displayId'   => $r->display_id,
            'wsId'        => $r->ws_id,
            'wgId'        => $r->wg_id,
            'name'        => $r->name,
            'pattern'     => $r->regulation ?? '-',
            'displayName' => trim(($r->manufacturer ?? '') . ' ' . ($r->model ?? '')) . ' (' . ($r->serial ?? '') . ')',
            'wsName'      => $r->ws_name  ?? '-',
            'wgName'      => $r->wg_name  ?? '-',
            'facName'     => $r->fac_name ?? '-',
            'result'      => $r->result == 2 ? 'passed' : 'failed',
            'timeTs'      => $r->time ? (int) $r->time : null,
            'timeIso'     => $r->time ? \Carbon\Carbon::createFromTimestampUTC((int) $r->time)->format('Y-m-d\TH:i:s') : null,
            'time'        => $r->time ? \Carbon\Carbon::createFromTimestampUTC((int) $r->time)->format('Y-m-d H:i') : '-',
        ]);

        return response()->json(['data' => $data, 'total' => $total]);
    }

    // ─── API: TASKS ──────────────────────────────────────────────────────────
    public function api_tasks(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        $role = $request->session()->get('role');
        if (!$user) return response()->json(['data' => [], 'total' => 0]);

        $requestedFacilityId = $request->get('facility_id');
        $facility_id    = $role === 'super'
            ? ($requestedFacilityId !== null && $requestedFacilityId !== '' ? $requestedFacilityId : null)
            : $user->facility_id;
        $workgroup_id   = $request->get('workgroup_id');
        $workstation_id = $request->get('workstation_id');
        $display_id     = $request->get('display_id');
        $display_ids    = collect(explode(',', (string) $request->get('display_ids', '')))->filter()->values()->all();
        $search = $request->get('search', '');
        $sortMode = $request->get('sort_mode', 'due');
        $sort = (string) $request->get('sort', '');
        $order = strtolower((string) $request->get('order', 'asc')) === 'desc' ? 'desc' : 'asc';
        $dueScope = $request->get('due_scope');
        $nowTimestamp = now()->timestamp;
        $withoutTotal = $request->boolean('without_total');
        $limit  = max(1, min((int)$request->get('limit', 25), 100));
        $page   = max(1, (int)$request->get('page', 1));
        $offset = ($page - 1) * $limit;

        $taskFallbackDueExpression = "COALESCE(
            UNIX_TIMESTAMP(CONVERT_TZ(
                STR_TO_DATE(CONCAT(REPLACE(tasks.startdate, '.', '-'), ' ', LEFT(tasks.starttime, 5)), '%Y-%m-%d %H:%i'),
                COALESCE(facilities.timezone, 'UTC'),
                '+00:00'
            )),
            UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(REPLACE(tasks.startdate, '.', '-'), ' ', LEFT(tasks.starttime, 5)), '%Y-%m-%d %H:%i'))
        )";
        $taskDueExpression = "COALESCE(NULLIF(tasks.nextrun, 0), {$taskFallbackDueExpression})";

        $taskRows = DB::table('tasks')
            ->join('displays', 'tasks.display_id', '=', 'displays.id')
            ->join('workstations', 'displays.workstation_id', '=', 'workstations.id')
            ->join('workgroups', 'workstations.workgroup_id', '=', 'workgroups.id')
            ->join('facilities', 'workgroups.facility_id', '=', 'facilities.id')
            ->leftJoin('task_types', 'tasks.type', '=', 'task_types.key')
            ->leftJoin('schedule_types', 'tasks.schtype', '=', 'schedule_types.client_id')
            ->where('tasks.deleted', 0)
            ->whereNotNull('tasks.type')
            ->whereNotNull('tasks.schtype')
            ->tap(fn ($q) => $this->applyCompletedOneShotTaskVisibilityGuard($q))
            ->whereRaw($taskDueExpression . ' > 0')
            ->when($dueScope === 'due', fn($q) => $q->whereRaw($taskDueExpression . ' <= ?', [$nowTimestamp]))
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->when($workgroup_id, fn($q) => $q->where('workgroups.id', $workgroup_id))
            ->when($workstation_id, fn($q) => $q->where('workstations.id', $workstation_id))
            ->when($display_id, fn($q) => $q->where('tasks.display_id', $display_id))
            ->when(!empty($display_ids), fn($q) => $q->whereIn('tasks.display_id', $display_ids))
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('displays.manufacturer', 'like', "%$search%")
                    ->orWhere('displays.model', 'like', "%$search%")
                    ->orWhere('displays.serial', 'like', "%$search%")
                    ->orWhere('workstations.name', 'like', "%$search%")
                    ->orWhere('workgroups.name', 'like', "%$search%")
                    ->orWhere('facilities.name', 'like', "%$search%")
                    ->orWhere('task_types.title', 'like', "%$search%")
                    ->orWhere('schedule_types.title', 'like', "%$search%");
            }))
            ->select([
                DB::raw("'task' as record_type"),
                'tasks.id',
                'tasks.display_id',
                'tasks.type as task_type_key',
                'tasks.schtype as schedule_type_id',
                'tasks.status',
                'tasks.lastrun',
                'tasks.disabled',
                'tasks.deleted',
                'task_types.title as task_name',
                'schedule_types.title as schedule_name',
                DB::raw($taskDueExpression . ' as due_at'),
                DB::raw($taskDueExpression . ' as computed_due_at'),
                'tasks.startdate',
                'tasks.starttime',
                'tasks.created_at as created_at',
                'displays.manufacturer',
                'displays.model',
                'displays.serial',
                'displays.workstation_id',
                'workstations.id as ws_id',
                'workgroups.id as wg_id',
                'facilities.id as fac_id',
                'workstations.name as ws_name',
                'workgroups.name as wg_name',
                'facilities.name as fac_name',
                'facilities.timezone as fac_timezone',
            ]);

        $qaRows = DB::table('qa_tasks')
            ->join('displays', 'qa_tasks.display_id', '=', 'displays.id')
            ->join('workstations', 'displays.workstation_id', '=', 'workstations.id')
            ->join('workgroups', 'workstations.workgroup_id', '=', 'workgroups.id')
            ->join('facilities', 'workgroups.facility_id', '=', 'facilities.id')
            ->where('qa_tasks.deleted', 0)
            ->where('qa_tasks.nextdate', '>', 0)
            ->where('qa_tasks.nextdate', '<', 4294967295)
            ->when($dueScope === 'due', fn($q) => $q->where('qa_tasks.nextdate', '<=', $nowTimestamp))
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->when($workgroup_id, fn($q) => $q->where('workgroups.id', $workgroup_id))
            ->when($workstation_id, fn($q) => $q->where('workstations.id', $workstation_id))
            ->when($display_id, fn($q) => $q->where('qa_tasks.display_id', $display_id))
            ->when(!empty($display_ids), fn($q) => $q->whereIn('qa_tasks.display_id', $display_ids))
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('displays.manufacturer', 'like', "%$search%")
                    ->orWhere('displays.model', 'like', "%$search%")
                    ->orWhere('displays.serial', 'like', "%$search%")
                    ->orWhere('workstations.name', 'like', "%$search%")
                    ->orWhere('workgroups.name', 'like', "%$search%")
                    ->orWhere('facilities.name', 'like', "%$search%")
                    ->orWhere('qa_tasks.name', 'like', "%$search%");
            }));

        $qaRows = $this->applyQaTaskVisibilityGuard($qaRows)
            ->select([
                DB::raw("'qa_task' as record_type"),
                'qa_tasks.id',
                'qa_tasks.display_id',
                DB::raw('NULL as task_type_key'),
                DB::raw('0 as schedule_type_id'),
                DB::raw('0 as status'),
                DB::raw('0 as lastrun'),
                DB::raw('0 as disabled'),
                DB::raw('0 as deleted'),
                'qa_tasks.name as task_name',
                'qa_tasks.freq as schedule_name',
                'qa_tasks.nextdate as due_at',
                'qa_tasks.nextdate as computed_due_at',
                DB::raw('NULL as startdate'),
                DB::raw('NULL as starttime'),
                'qa_tasks.created_at as created_at',
                'displays.manufacturer',
                'displays.model',
                'displays.serial',
                'displays.workstation_id',
                'workstations.id as ws_id',
                'workgroups.id as wg_id',
                'facilities.id as fac_id',
                'workstations.name as ws_name',
                'workgroups.name as wg_name',
                'facilities.name as fac_name',
                'facilities.timezone as fac_timezone',
            ]);

        $buildAllRowsQuery = function () use ($taskRows, $qaRows) {
            return DB::query()->fromSub((clone $taskRows)->unionAll(clone $qaRows), 'all_scheduler_rows');
        };

        $rowsQuery = $buildAllRowsQuery();
        if ($sort !== '') {
            switch ($sort) {
                case 'task_name':
                    $rowsQuery->orderBy('task_name', $order);
                    break;
                case 'display_name':
                    $rowsQuery->orderBy('manufacturer', $order)
                        ->orderBy('model', $order)
                        ->orderBy('serial', $order);
                    break;
                case 'schedule_name':
                    $rowsQuery->orderBy('schedule_name', $order);
                    break;
                case 'due_at':
                    $rowsQuery->orderBy('computed_due_at', $order);
                    break;
                case 'created_at':
                    $rowsQuery->orderBy('created_at', $order);
                    break;
                default:
                    break;
            }
            $rowsQuery->orderBy('id', $order === 'asc' ? 'asc' : 'desc');
        } else {
            switch ($sortMode) {
                case 'latest':
                    $rowsQuery->orderBy('created_at', 'desc')->orderBy('id', 'desc');
                    break;
                case 'due_desc':
                    $rowsQuery->orderBy('computed_due_at', 'desc')->orderBy('id', 'desc');
                    break;
                default:
                    $rowsQuery
                        ->orderByRaw('CASE WHEN computed_due_at >= ? THEN 0 ELSE 1 END', [$nowTimestamp])
                        ->orderByRaw('CASE WHEN computed_due_at >= ? THEN computed_due_at END ASC', [$nowTimestamp])
                        ->orderByRaw('CASE WHEN computed_due_at < ? THEN computed_due_at END DESC', [$nowTimestamp])
                        ->orderBy('id', 'desc');
                    break;
            }
        }

        $countCacheKey = 'scheduler:tasks:count:' . md5(json_encode([
            'facility_id' => $facility_id,
            'workgroup_id' => $workgroup_id,
            'workstation_id' => $workstation_id,
            'display_id' => $display_id,
            'display_ids' => $display_ids,
            'search' => $search,
            'due_scope' => $dueScope,
        ]));

        $total = $withoutTotal
            ? null
            : cache()->remember($countCacheKey, now()->addSeconds(20), function () use ($buildAllRowsQuery) {
                return $buildAllRowsQuery()->count();
            });
        $rows = $rowsQuery->offset($offset)->limit($limit)->get();

        $data = $rows->map(function($r) {
            $ts = (int) ($r->computed_due_at ?? 0);
            $displayTimezone = $r->fac_timezone ?: config('app.timezone', 'UTC');
            $dueFormatted = $this->formatRecordDueAt($r, $ts);
            if (
                ($r->record_type ?? null) === 'task'
                && (int) ($r->schedule_type_id ?? -1) === \App\Models\ScheduleType::STARTUP
                && !empty($r->startdate)
            ) {
                try {
                    $dueFormatted = \Carbon\Carbon::createFromFormat(
                        'Y-m-d',
                        str_replace('.', '-', (string) $r->startdate),
                        $displayTimezone
                    )->format('d M Y');
                } catch (\Throwable $e) {
                    $dueFormatted = $this->formatRecordDueAt($r, $ts);
                }
            }
            $createdFormatted = $r->created_at
                ? \Carbon\Carbon::parse($r->created_at, config('app.timezone', 'UTC'))->setTimezone($displayTimezone)->format('d M Y H:i')
                : 'Not recorded';
            $lastExecutionFormatted = $this->formatSchedulerLastExecutionAt($r);
            $dueObj = $ts
                ? \Carbon\Carbon::createFromTimestampUTC($ts)->setTimezone($displayTimezone)
                : null;
            $isPast = $dueObj ? $dueObj->isPast() : false;
            $isSoon = $dueObj ? (!$isPast && $dueObj->diffInDays(\Carbon\Carbon::now($r->fac_timezone ?: config('app.timezone', 'UTC'))) <= 7) : false;
            $derivedStatus = $this->resolveSchedulerStatus($r, $ts, $isPast);
            $enabled = ((int) ($r->disabled ?? 0) !== 1) && ((int) ($r->deleted ?? 0) !== 1);
            return [
                'id'          => $r->id,
                'type'        => $r->record_type,
                'displayId'   => $r->display_id,
                'displayName' => trim(($r->manufacturer ?? '') . ' ' . ($r->model ?? '')) . ' (' . ($r->serial ?? '') . ')',
                'wsId'        => $r->ws_id ?? $r->workstation_id,
                'wgId'        => $r->wg_id ?? null,
                'facId'       => $r->fac_id ?? null,
                'wsName'      => $r->ws_name  ?? '-',
                'wgName'      => $r->wg_name  ?? '-',
                'facName'     => $r->fac_name ?? '-',
                'taskName'    => $this->resolveTaskTypeLabel($r->task_type_key ?? null, $r->task_name ?? null, $r->record_type ?? null),
                'scheduleName'=> $r->schedule_name ?? '-',
                'lastExecutionAt' => $lastExecutionFormatted,
                'createdAt'   => $createdFormatted,
                'dueAt'       => $dueFormatted,
                'dueColor'    => $isPast ? 'danger' : ($isSoon ? 'warning' : 'success'),
                'status'      => $derivedStatus['label'],
                'statusColor' => $derivedStatus['tone'],
                'enabledLabel'=> $enabled ? 'Enabled' : 'Disabled',
                'enabledColor'=> $enabled ? 'success' : 'slate',
            ];
        });

        return response()->json([
            'data' => $data,
            'total' => $total,
        ]);
    }

    public function api_calibration_tasks(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        if (!$user) return response()->json(['data' => [], 'total' => 0]);

        $facility_id    = $request->get('facility_id', $user->facility_id);
        $workgroup_id   = $request->get('workgroup_id');
        $workstation_id = $request->get('workstation_id');
        $display_id     = $request->get('display_id');
        $display_ids    = collect(explode(',', (string) $request->get('display_ids', '')))->filter()->values()->all();
        $search = $request->get('search', '');
        $sort   = (string) $request->get('sort', 'created_at');
        $order  = strtolower((string) $request->get('order', 'desc')) === 'asc' ? 'asc' : 'desc';
        $limit  = (int) $request->get('limit', 25);
        $page   = (int) $request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $query = DB::table('tasks')
            ->join('displays', 'tasks.display_id', '=', 'displays.id')
            ->join('workstations', 'displays.workstation_id', '=', 'workstations.id')
            ->join('workgroups', 'workstations.workgroup_id', '=', 'workgroups.id')
            ->join('facilities', 'workgroups.facility_id', '=', 'facilities.id')
            ->leftJoin('users', 'tasks.user_id', '=', 'users.id')
            ->leftJoin('task_types', 'tasks.type', '=', 'task_types.key')
            ->leftJoin('schedule_types', 'tasks.schtype', '=', 'schedule_types.client_id')
            ->where('tasks.type', 'cal')
            ->where('tasks.deleted', 0)
            ->where('tasks.disabled', 0)
            ->where('tasks.nextrun', '>', 0)
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->when($workgroup_id, fn($q) => $q->where('workgroups.id', $workgroup_id))
            ->when($workstation_id, fn($q) => $q->where('workstations.id', $workstation_id))
            ->when($display_id, fn($q) => $q->where('displays.id', $display_id))
            ->when(!empty($display_ids), fn($q) => $q->whereIn('displays.id', $display_ids))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('displays.manufacturer', 'like', "%$search%")
                    ->orWhere('displays.model', 'like', "%$search%")
                    ->orWhere('displays.serial', 'like', "%$search%")
                    ->orWhere('workstations.name', 'like', "%$search%")
                    ->orWhere('workgroups.name', 'like', "%$search%")
                    ->orWhere('facilities.name', 'like', "%$search%")
                    ->orWhere('task_types.title', 'like', "%$search%")
                    ->orWhere('schedule_types.title', 'like', "%$search%");
            }))
              ->select([
                  'tasks.id',
                  'tasks.display_id',
                  'workstations.id as ws_id',
                  'workgroups.id as wg_id',
                  'facilities.id as fac_id',
                  'tasks.nextrun as due_at',
                  'tasks.created_at',
                  'tasks.status',
                  'tasks.disabled',
                  'tasks.deleted',
                  'tasks.schtype',
                  'tasks.user_id',
                  'tasks.startdate',
                  'tasks.starttime',
                  'task_types.title as task_name',
                  'schedule_types.title as schedule_name',
                  'displays.manufacturer',
                  'displays.model',
                  'displays.serial',
                  'workstations.name as ws_name',
                  'workgroups.name as wg_name',
                  'facilities.name as fac_name',
                  'facilities.timezone as fac_timezone',
                  'users.fullname as created_by_fullname',
                  'users.name as created_by_name',
                  'users.email as created_by_email',
              ]);

        $total = $query->count();

        $rowsQuery = (clone $query);
        switch ($sort) {
            case 'display_name':
                $rowsQuery->orderByRaw(
                    "LOWER(CONCAT(COALESCE(displays.manufacturer,''), ' ', COALESCE(displays.model,''), ' ', COALESCE(displays.serial,''))) {$order}"
                );
                break;
            case 'task_name':
                $rowsQuery->orderBy('task_types.title', $order);
                break;
            case 'schedule_name':
                $rowsQuery->orderBy('schedule_types.title', $order);
                break;
            case 'due_at':
                $rowsQuery->orderBy('tasks.nextrun', $order);
                break;
            case 'created_at':
            default:
                $rowsQuery->orderBy('tasks.created_at', $order);
                break;
        }

        $rows = $rowsQuery
            ->orderBy('tasks.id', $order === 'asc' ? 'asc' : 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $data = $rows->map(function ($r) {
            $ts = (int) $r->due_at;
            $displayTimezone = $r->fac_timezone ?: config('app.timezone', 'UTC');
            $due = $ts ? \Carbon\Carbon::createFromTimestampUTC($ts)->setTimezone($displayTimezone) : null;
            $dueAt = $due ? $due->format('d M Y H:i') : '-';
            if ((int) ($r->schtype ?? -1) === \App\Models\ScheduleType::STARTUP && !empty($r->startdate)) {
                try {
                    $dueAt = \Carbon\Carbon::createFromFormat(
                        'Y-m-d',
                        str_replace('.', '-', (string) $r->startdate),
                        $displayTimezone
                    )->format('d M Y');
                } catch (\Throwable $e) {
                    $dueAt = $due ? $due->format('d M Y H:i') : '-';
                }
            }
            return [
                  'id' => $r->id,
                  'displayId' => $r->display_id,
                  'workstationId' => $r->ws_id,
                  'workgroupId' => $r->wg_id,
                  'facilityId' => $r->fac_id,
                  'displayName' => trim(($r->manufacturer ?? '') . ' ' . ($r->model ?? '')) . ' (' . ($r->serial ?? '') . ')',
                  'wsName' => $r->ws_name ?? '-',
                  'wgName' => $r->wg_name ?? '-',
                  'facName' => $r->fac_name ?? '-',
                  'taskName' => $r->task_name ?: 'Calibration',
                  'scheduleName' => $r->schedule_name ?: 'Manual',
                  'dueAt' => $dueAt,
                  'dueAtTs' => $ts ?: null,
                  'dueColor' => $due && $due->isPast() ? 'danger' : 'neutral',
                  'createdAt' => $r->created_at ? \Carbon\Carbon::parse($r->created_at, config('app.timezone'))->setTimezone($displayTimezone)->format('d M Y H:i') : 'Not recorded',
                  'createdBy' => $r->created_by_fullname ?: ($r->created_by_name ?: ($r->created_by_email ?: 'System')),
                  'statusLabel' => ((int) ($r->disabled ?? 0) === 1 || (int) ($r->deleted ?? 0) === 1) ? 'Inactive' : (((int) ($r->status ?? 0) === 1) ? 'Running' : 'Active'),
                  'statusTone' => ((int) ($r->disabled ?? 0) === 1 || (int) ($r->deleted ?? 0) === 1) ? 'slate' : (((int) ($r->status ?? 0) === 1) ? 'emerald' : 'sky'),
                  'startAt' => trim(($r->startdate ?? '') . ' ' . ($r->starttime ?? '')),
              ];
          });

        return response()->json(['data' => $data, 'total' => $total]);
    }

    // ─── API: ALERTS ─────────────────────────────────────────────────────────
    public function api_alerts(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        if (!$user) return response()->json(['data' => [], 'total' => 0]);

        $requestedFacilityId = $request->get('facility_id');
        $userRole    = \App\Helpers\AuthHelper::getCurrentUserRole();
        $facility_id = $userRole === 'super'
            ? ($requestedFacilityId !== null && $requestedFacilityId !== '' ? $requestedFacilityId : null)
            : $user->facility_id;
        $search = $request->get('search', '');
        $limit  = (int)$request->get('limit', 25);
        $page   = (int)$request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $query = DB::table('alerts')
            ->leftJoin('facilities', 'facilities.id', '=', 'alerts.facility_id')
            ->select(['alerts.id', 'alerts.email', 'alerts.actived', 'alerts.daily_report',
                      'alerts.facility_id', 'facilities.name as fac_name'])
            ->when($facility_id, fn($q) => $q->where('alerts.facility_id', $facility_id))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('alerts.email',    'like', "%$search%")
                   ->orWhere('facilities.name','like', "%$search%");
            }));

        $total = $query->count();
        $rows  = (clone $query)->orderBy('alerts.email')->offset($offset)->limit($limit)->get();

        $data = $rows->map(fn($r) => [
            'id'           => $r->id,
            'email'        => $r->email,
            'facilityId'   => $r->facility_id,
            'facName'      => $r->fac_name ?? '-',
            'dailyReport'  => (bool)$r->daily_report,
            'active'       => (bool)$r->actived,
        ]);

        return response()->json(['data' => $data, 'total' => $total]);
    }

    // ─── API: ALERTS TOGGLE ──────────────────────────────────────────────────
    public function api_alerts_toggle(Request $request, $id)
    {
        $userId = $request->session()->get('id');
        $user = \App\Models\User::find($userId);
        $userRole = \App\Helpers\AuthHelper::getCurrentUserRole();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!in_array($userRole, ['super', 'admin'], true)) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $alert = \App\Models\Alert::findOrFail($id);

        if ($userRole !== 'super' && (int) $alert->facility_id !== (int) $user->facility_id) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $alert->update(['actived' => !$alert->actived]);
        return response()->json(['success' => true, 'active' => (bool) $alert->actived]);
    }

    // ─── API: FACILITIES ─────────────────────────────────────────────────────
    public function api_facilities(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        $role = $request->session()->get('role');
        if (!$user) return response()->json(['data' => [], 'total' => 0]);

        $type = $request->get('type');
        $search = $request->get('search', '');
        $sort = (string) $request->get('sort', 'name');
        $order = strtolower((string) $request->get('order', 'asc')) === 'desc' ? 'desc' : 'asc';
        $limit  = (int)$request->get('limit', 25);
        $page   = (int)$request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $query = DB::table('facilities')
            ->leftJoin(DB::raw('(SELECT facility_id, COUNT(*) as cnt FROM workgroups GROUP BY facility_id) wgc'),
                'wgc.facility_id', '=', 'facilities.id')
            ->leftJoin(DB::raw('(SELECT facility_id, COUNT(*) as cnt FROM users GROUP BY facility_id) uc'),
                'uc.facility_id', '=', 'facilities.id')
            ->leftJoin(DB::raw('(
                SELECT
                    w2.facility_id,
                    COUNT(d.id) as cnt,
                    SUM(CASE WHEN d.status = 1 THEN 1 ELSE 0 END) as ok_cnt,
                    SUM(CASE WHEN d.status = 2 THEN 1 ELSE 0 END) as failed_cnt
                FROM displays d
                JOIN workstations ws ON ws.id = d.workstation_id
                JOIN workgroups w2 ON w2.id = ws.workgroup_id
                GROUP BY w2.facility_id
            ) dc'),
                'dc.facility_id', '=', 'facilities.id')
            ->select([
                'facilities.id', 'facilities.name', 'facilities.location', 'facilities.timezone',
                DB::raw('COALESCE(wgc.cnt, 0) as workgroups_count'),
                DB::raw('COALESCE(uc.cnt, 0) as users_count'),
                DB::raw('COALESCE(dc.cnt, 0) as displays_count'),
                DB::raw('COALESCE(dc.ok_cnt, 0) as ok_displays_count'),
                DB::raw('COALESCE(dc.failed_cnt, 0) as failed_displays_count'),
            ])
            ->whereNull('facilities.deleted_at')
            ->when($role !== 'super', fn($q) => $q->where('facilities.id', $user->facility_id))
            ->when($type === 'ok', fn($q) => $q->whereRaw('COALESCE(dc.failed_cnt, 0) = 0 AND COALESCE(dc.ok_cnt, 0) = COALESCE(dc.cnt, 0) AND COALESCE(dc.cnt, 0) > 0'))
            ->when($type === 'failed', fn($q) => $q->whereRaw('COALESCE(dc.failed_cnt, 0) > 0'))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('facilities.name',     'like', "%$search%")
                   ->orWhere('facilities.timezone','like', "%$search%");
            }));

        $total = $query->count();
        $sortableMap = [
            'name' => 'facilities.name',
            'location' => 'facilities.location',
            'timezone' => 'facilities.timezone',
            'workgroupsCount' => DB::raw('COALESCE(wgc.cnt, 0)'),
            'usersCount' => DB::raw('COALESCE(uc.cnt, 0)'),
            'displaysCount' => DB::raw('COALESCE(dc.cnt, 0)'),
        ];
        $sortColumn = $sortableMap[$sort] ?? 'facilities.name';

        $rows  = (clone $query)
            ->orderBy($sortColumn, $order)
            ->orderBy('facilities.id', 'asc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $data = $rows->map(fn($r) => [
            'id'               => $r->id,
            'name'             => $r->name,
            'location'         => $r->location ?? '-',
            'timezone'         => $r->timezone ?? '-',
            'workgroupsCount'  => (int)$r->workgroups_count,
            'usersCount'       => (int)$r->users_count,
            'displaysCount'    => (int)$r->displays_count,
            'okDisplaysCount'  => (int)$r->ok_displays_count,
            'failedDisplaysCount' => (int)$r->failed_displays_count,
            'displayHealth'    => (int)$r->failed_displays_count > 0
                ? 'failed'
                : (((int)$r->displays_count > 0) && ((int)$r->ok_displays_count === (int)$r->displays_count) ? 'ok' : 'unknown'),
            'canManage'        => in_array($role, ['super', 'admin'], true),
            'canDelete'        => $role === 'super',
        ]);

        return response()->json(['data' => $data, 'total' => $total]);
    }
}
