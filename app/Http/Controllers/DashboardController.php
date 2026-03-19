<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{
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

        $baseDisplays = \App\Models\Display::when($facility_id, function ($q) use ($facility_id) {
            return $q->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                ->where('workgroups.facility_id', '=', $facility_id);
        })
            ->join('display_preferences', 'display_preferences.display_id', '=', 'displays.id')
            ->where(['display_preferences.name' => 'exclude', 'display_preferences.value' => '0']);
        
        $d_ok = (clone $baseDisplays)->where('displays.status', 1)->count();
        $d_fail = (clone $baseDisplays)->where('displays.status', 2)->count();
        
        $d_fail_recent=array(); $i=0;
            
        //$workstations=\App\Models\Workstation::whereIn('workgroup_id', $workgroups_ids)->count();
        $workstations = \App\Models\Workstation::when($facility_id, function ($q) use ($facility_id) {
            return $q->join('workgroups', 'workgroups.id', '=', 'workgroup_id')
                ->where('workgroups.facility_id', '=', $facility_id);
        })
            ->count();
        
        //$display_ids=\App\Models\Display::whereIn('workstation_id', $workstations_ids)->pluck('id');
        
        
        //total due tasks
        $ids = request()->input('id') ? explode(',', request()->input('id')) : [];

        
        // Calculate due tasks count (Tasks + QA Tasks)
        $tasksCount = \App\Models\Task::where('tasks.deleted', 0)
            ->where('tasks.disabled', 0)
            ->where('tasks.nextrun', '>', 0)
            ->whereHas('display.preferences', function ($q) {
                $q->where('name', 'exclude')->where('value', '0');
            })
            ->when($facility_id, function ($q) use ($facility_id) {
                return $q->whereHas('display.workstation.workgroup', fn($qw) => $qw->where('facility_id', $facility_id));
            })
            ->count();

        $qaTasksCount = \App\Models\QATask::where('qa_tasks.deleted', 0)
            ->where('qa_tasks.nextdate', '>', 0)
            ->whereHas('display.preferences', function ($q) {
                $q->where('name', 'exclude')->where('value', '0');
            })
            ->when($facility_id, function ($q) use ($facility_id) {
                return $q->whereHas('display.workstation.workgroup', fn($qw) => $qw->where('facility_id', $facility_id));
            })
            ->count();

        $due_tasks = $tasksCount + $qaTasksCount;
        $due_tasks_recents = $due_tasks;
            
        $greetingName = explode(' ', trim($user->name ?? 'Administrator'))[0] ?: 'Administrator';

        return view('dashboard.dashboard', [
            'user' => $user,
            'greetingName' => $greetingName,
            'd_ok' => $d_ok,
            'd_fail' => $d_fail,
            'workstations' => $workstations,
            'due_tasks' => $due_tasks,
            'title' => 'Dashboard',
            'd_fail_recent' => $d_fail_recent,
            'due_tasks_recents' => $due_tasks_recents,
        ]);
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

    public function api_due_tasks(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        
        if (!$user) {
            return response()->json([]);
        }
        
        $facility_id = $user->facility_id;

        // Single-query JOIN approach for tasks — no N+1 eager loading
        $taskRows = DB::table('tasks')
            ->join('displays', 'tasks.display_id', '=', 'displays.id')
            ->join('workstations', 'displays.workstation_id', '=', 'workstations.id')
            ->join('workgroups', 'workstations.workgroup_id', '=', 'workgroups.id')
            ->join('facilities', 'workgroups.facility_id', '=', 'facilities.id')
            ->leftJoin('task_types', 'tasks.type', '=', 'task_types.key')
            ->leftJoin('schedule_types', 'tasks.schtype', '=', 'schedule_types.client_id')
            ->where('tasks.deleted', 0)
            ->where('tasks.disabled', 0)
            ->where('tasks.nextrun', '>', 0)
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->select([
                DB::raw("'task' as record_type"),
                'tasks.id', 'tasks.display_id',
                'task_types.title as task_name',
                'schedule_types.title as schedule_name',
                'tasks.nextrun as due_at',
                'displays.manufacturer', 'displays.model', 'displays.serial',
                'displays.workstation_id',
                'workstations.name as ws_name', 'workstations.workgroup_id as wg_id',
                'workgroups.name as wg_name', 'workgroups.facility_id as fac_id',
                'facilities.name as fac_name',
            ])
            ->orderBy('tasks.nextrun', 'asc')
            ->limit(10)
            ->get();

        // Single-query JOIN approach for QA tasks
        $qaRows = DB::table('qa_tasks')
            ->join('displays', 'qa_tasks.display_id', '=', 'displays.id')
            ->join('workstations', 'displays.workstation_id', '=', 'workstations.id')
            ->join('workgroups', 'workstations.workgroup_id', '=', 'workgroups.id')
            ->join('facilities', 'workgroups.facility_id', '=', 'facilities.id')
            ->where('qa_tasks.nextdate', '>', 0)
            ->where('qa_tasks.deleted', 0)
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->select([
                DB::raw("'qa_task' as record_type"),
                'qa_tasks.id', 'qa_tasks.display_id',
                'qa_tasks.name as task_name',
                'qa_tasks.freq as schedule_name',
                'qa_tasks.nextdate as due_at',
                'displays.manufacturer', 'displays.model', 'displays.serial',
                'displays.workstation_id',
                'workstations.name as ws_name', 'workstations.workgroup_id as wg_id',
                'workgroups.name as wg_name', 'workgroups.facility_id as fac_id',
                'facilities.name as fac_name',
            ])
            ->orderBy('qa_tasks.nextdate', 'asc')
            ->limit(10)
            ->get();

        // Merge both sets and take the 10 soonest due items
        $allTasks = $taskRows->merge($qaRows)
            ->sortBy('due_at')
            ->take(10);

        $formattedData = $allTasks->map(function ($record) {
            $mfg = $record->manufacturer ?? '';
            $mod = $record->model ?? '';
            $ser = $record->serial ?? '';
            $displayName = trim($mfg . ' ' . $mod . ' (' . $ser . ')');
            
            $dueAtTimestamp = (int)$record->due_at;
            $dueAtFormatted = $dueAtTimestamp ? \Carbon\Carbon::createFromTimestamp($dueAtTimestamp)->format('d M Y H:i') : '-';
            
            $isPast = false;
            $isToday = false;
            $diffForHumans = '-';
            if ($dueAtTimestamp) {
                $dueObj = \Carbon\Carbon::createFromTimestamp($dueAtTimestamp);
                $isPast = $dueObj->isPast();
                $isToday = $dueObj->isToday();
                $diffForHumans = $isPast ? $dueObj->diffForHumans() : 'Not overdue';
            }

            return [
                'displayId'  => $record->display_id,
                'wsId'       => $record->workstation_id,
                'wgId'       => $record->wg_id,
                'facId'      => $record->fac_id,
                'displayName' => $displayName,
                'wsName'     => $record->ws_name ?? '-',
                'wgName'     => $record->wg_name ?? '-',
                'facName'    => $record->fac_name ?? '-',
                'task'       => $record->task_name ?? $record->record_type,
                'schedule'   => $record->schedule_name ?? '-',
                'dueAt'      => $dueAtFormatted,
                'timestamp'  => $dueAtTimestamp,
                'isPast'     => $isPast,
                'isToday'    => $isToday,
                'overdue'    => $diffForHumans,
            ];
        })->values();

        return response()->json($formattedData);
    }

    public function api_displays_failed(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        
        if (!$user) {
            return response()->json([]);
        }

        $facility_id = $user->facility_id;

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
            ->limit(10)
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

        $facility_id = $user->facility_id;

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
                'displays.manufacturer',
                'displays.model',
                'workstations.name as ws_name',
                'workgroups.name as wg_name',
                'facilities.name as fac_name',
            ])
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->orderBy('histories.time', 'desc')
            ->limit(10)
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

    // ─── API: DISPLAYS ───────────────────────────────────────────────────────
    public function api_displays(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        $role = $request->session()->get('role');
        if (!$user) return response()->json(['data' => [], 'total' => 0]);

        $facility_id = $request->get('facility_id', $user->facility_id);
        $workstation_id = $request->get('workstation_id');
        $workgroup_id   = $request->get('workgroup_id');
        $display_ids    = collect(explode(',', (string) $request->get('display_ids', '')))->filter()->values()->all();
        $type    = $request->get('type');
        $status  = $request->get('status');
        $search  = $request->get('search', '');
        $sort    = $request->get('sort', 'manufacturer');
        $order   = $request->get('order', 'asc');
        $limit   = (int)$request->get('limit', 25);
        $page    = (int)$request->get('page', 1);
        $offset  = ($page - 1) * $limit;

        $query = DB::table('displays')
            ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
            ->join('workgroups',   'workgroups.id',   '=', 'workstations.workgroup_id')
            ->join('facilities',   'facilities.id',   '=', 'workgroups.facility_id')
            ->leftJoin('display_preferences as exclude_pref', function ($join) {
                $join->on('exclude_pref.display_id', '=', 'displays.id')
                    ->where('exclude_pref.name', '=', 'exclude');
            })
            ->select([
                'displays.id', 'displays.manufacturer', 'displays.model', 'displays.serial',
                'displays.status', 'displays.connected', 'displays.workstation_id',
                'displays.updated_at', 'displays.errors',
                'workstations.name as ws_name', 'workstations.workgroup_id as wg_id',
                'workgroups.name as wg_name', 'workgroups.facility_id as fac_id',
                'workgroups.address as wg_address', 'workgroups.city as wg_city', 'workgroups.state as wg_state',
                'facilities.name as fac_name',
            ])
            ->where(function ($q) {
                $q->whereNull('exclude_pref.value')
                    ->orWhere('exclude_pref.value', '0');
            })
            ->when($facility_id,    fn($q) => $q->where('workgroups.facility_id',  $facility_id))
            ->when($workstation_id, fn($q) => $q->where('displays.workstation_id', $workstation_id))
            ->when($workgroup_id,   fn($q) => $q->where('workstations.workgroup_id', $workgroup_id))
            ->when(!empty($display_ids), fn($q) => $q->whereIn('displays.id', $display_ids))
            ->when($type === 'ok', fn($q) => $q->where('displays.status', 1))
            ->when($type === 'failed', fn($q) => $q->where('displays.status', 2))
            ->when($status !== null && $status !== '', fn($q) => $q->where('displays.status', $status))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('displays.manufacturer', 'like', "%$search%")
                   ->orWhere('displays.model',      'like', "%$search%")
                   ->orWhere('displays.serial',     'like', "%$search%")
                   ->orWhere('workstations.name',   'like', "%$search%")
                   ->orWhere('workgroups.name',     'like', "%$search%")
                   ->orWhere('facilities.name',     'like', "%$search%");
            }));

        $sortColumn = match ($sort) {
            'status' => 'displays.status',
            'updated_at' => 'displays.updated_at',
            default => 'displays.manufacturer',
        };

        $total = $query->count();
        $rows  = (clone $query)->orderBy($sortColumn, $order)
            ->offset($offset)->limit($limit)->get();

        $data = $rows->map(function ($r) use ($role) {
            $parts = array_filter([$r->wg_address, $r->wg_city, $r->wg_state]);
            $errors = json_decode($r->errors ?? '[]', true);

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
                'updatedAt'   => $r->updated_at ? \Carbon\Carbon::parse($r->updated_at)->format('d M Y H:i') : '-',
                'errors'      => is_array($errors) ? $errors : [],
                'canManage'   => in_array($role, ['super', 'admin'], true),
                'canDelete'   => in_array($role, ['super', 'admin'], true),
            ];
        });

        return response()->json(['data' => $data, 'total' => $total]);
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
        $search = $request->get('search', '');
        $limit  = (int)$request->get('limit', 25);
        $page   = (int)$request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $query = DB::table('workstations')
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->leftJoin(DB::raw('(SELECT workstation_id, COUNT(*) as cnt FROM displays GROUP BY workstation_id) dc'),
                'dc.workstation_id', '=', 'workstations.id')
            ->select([
                'workstations.id', 'workstations.name', 'workstations.last_connected', 'workstations.sleep_time', 'workstations.workgroup_id as wg_id',
                'workgroups.name as wg_name', 'workgroups.facility_id as fac_id',
                'facilities.name as fac_name',
                DB::raw('COALESCE(dc.cnt, 0) as displays_count'),
            ])
            ->when($facility_id,  fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->when($workgroup_id, fn($q) => $q->where('workstations.workgroup_id', $workgroup_id))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('workstations.name', 'like', "%$search%")
                   ->orWhere('workgroups.name', 'like', "%$search%")
                   ->orWhere('facilities.name', 'like', "%$search%");
            }));

        $total = $query->count();
        $rows  = (clone $query)->orderBy('workstations.name')->offset($offset)->limit($limit)->get();

        $data = $rows->map(function($r) use ($role) {
            $lc = $r->last_connected ? \Carbon\Carbon::parse($r->last_connected) : null;
            $lcFormatted = $lc ? $lc->format('d M Y H:i') : '-';
            $daysSince = $lc ? $lc->diffInDays(\Carbon\Carbon::now()) : 999;
            $lcColor = $daysSince > 15 ? 'danger' : ($daysSince > 7 ? 'warning' : 'success');
            return [
                'id'            => $r->id,
                'name'          => $r->name,
                'wgId'          => $r->wg_id,
                'wgName'        => $r->wg_name  ?? '-',
                'facId'         => $r->fac_id,
                'facName'       => $r->fac_name ?? '-',
                'sleepTime'     => $r->sleep_time ?: 'Off',
                'lastConnected' => $lcFormatted,
                'lcColor'       => $lcColor,
                'displaysCount' => (int)$r->displays_count,
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
        $search = $request->get('search', '');
        $limit  = (int)$request->get('limit', 25);
        $page   = (int)$request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $query = DB::table('workgroups')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->leftJoin(DB::raw('(SELECT workgroup_id, COUNT(*) as cnt FROM workstations GROUP BY workgroup_id) wsc'),
                'wsc.workgroup_id', '=', 'workgroups.id')
            ->leftJoin(DB::raw('(SELECT workgroups.id as wg_id, COUNT(displays.id) as cnt FROM displays JOIN workstations ON workstations.id = displays.workstation_id JOIN workgroups ON workgroups.id = workstations.workgroup_id GROUP BY workgroups.id) dsc'),
                'dsc.wg_id', '=', 'workgroups.id')
            ->select([
                'workgroups.id', 'workgroups.name', 'workgroups.address', 'workgroups.phone', 'workgroups.facility_id as fac_id',
                'facilities.name as fac_name',
                DB::raw('COALESCE(wsc.cnt, 0) as workstations_count'),
                DB::raw('COALESCE(dsc.cnt, 0) as displays_count'),
            ])
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('workgroups.name', 'like', "%$search%")
                   ->orWhere('facilities.name', 'like', "%$search%");
            }));

        $total = $query->count();
        $rows  = (clone $query)->orderBy('workgroups.name')->offset($offset)->limit($limit)->get();

        $data = $rows->map(fn($r) => [
            'id'                => $r->id,
            'name'              => $r->name,
            'address'           => $r->address           ?? '-',
            'phone'             => $r->phone             ?? '-',
            'facId'             => $r->fac_id,
            'facName'           => $r->fac_name          ?? '-',
            'workstationsCount' => (int)$r->workstations_count,
            'displaysCount'     => (int)$r->displays_count,
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
        $limit  = (int)$request->get('limit', 25);
        $page   = (int)$request->get('page', 1);
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
                'workstations.name as ws_name',
                'workgroups.name as wg_name',
                'facilities.name as fac_name',
            ])
            ->when($facility_id,    fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->when($workgroup_id,   fn($q) => $q->where('workstations.workgroup_id', $workgroup_id))
            ->when($workstation_id, fn($q) => $q->where('displays.workstation_id', $workstation_id))
            ->when($display_id,     fn($q) => $q->where('histories.display_id', $display_id))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('displays.manufacturer', 'like', "%$search%")
                   ->orWhere('displays.model',      'like', "%$search%")
                   ->orWhere('displays.serial',     'like', "%$search%")
                   ->orWhere('workstations.name',   'like', "%$search%")
                   ->orWhere('facilities.name',     'like', "%$search%");
            }));

        $total = $query->count();
        $rows  = (clone $query)->orderBy('histories.time', 'desc')->offset($offset)->limit($limit)->get();

        $data = $rows->map(fn($r) => [
            'id'          => $r->id,
            'displayId'   => $r->display_id,
            'name'        => $r->name,
            'pattern'     => $r->regulation ?? '-',
            'displayName' => trim(($r->manufacturer ?? '') . ' ' . ($r->model ?? '')) . ' (' . ($r->serial ?? '') . ')',
            'wsName'      => $r->ws_name  ?? '-',
            'wgName'      => $r->wg_name  ?? '-',
            'facName'     => $r->fac_name ?? '-',
            'result'      => $r->result == 2 ? 'passed' : 'failed',
            'time'        => $r->time ? \Carbon\Carbon::createFromTimestamp($r->time)->format('Y-m-d H:i') : '-',
        ]);

        return response()->json(['data' => $data, 'total' => $total]);
    }

    // ─── API: TASKS ──────────────────────────────────────────────────────────
    public function api_tasks(Request $request)
    {
        $user_id = $request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        if (!$user) return response()->json(['data' => [], 'total' => 0]);

        $facility_id    = $request->get('facility_id', $user->facility_id);
        $workgroup_id   = $request->get('workgroup_id');
        $workstation_id = $request->get('workstation_id');
        $display_id     = $request->get('display_id');
        $search = $request->get('search', '');
        $sortMode = $request->get('sort_mode', 'due');
        $limit  = (int)$request->get('limit', 25);
        $page   = (int)$request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $taskRows = DB::table('tasks')
            ->join('displays',    'tasks.display_id',      '=', 'displays.id')
            ->join('workstations','displays.workstation_id','=', 'workstations.id')
            ->join('workgroups',  'workstations.workgroup_id','=','workgroups.id')
            ->join('facilities',  'workgroups.facility_id', '=', 'facilities.id')
            ->leftJoin('task_types',     'tasks.type',   '=', 'task_types.key')
            ->leftJoin('schedule_types', 'tasks.schtype','=', 'schedule_types.client_id')
            ->where('tasks.deleted', 0)
            ->where('tasks.nextrun', '>', 0)
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->when($workgroup_id, fn($q) => $q->where('workgroups.id', $workgroup_id))
            ->when($workstation_id, fn($q) => $q->where('workstations.id', $workstation_id))
            ->when($display_id,  fn($q) => $q->where('tasks.display_id', $display_id))
            ->when(!empty($display_ids), fn($q) => $q->whereIn('tasks.display_id', $display_ids))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('displays.manufacturer','like',"%$search%")
                   ->orWhere('displays.model',     'like',"%$search%")
                   ->orWhere('displays.serial',    'like',"%$search%")
                   ->orWhere('workstations.name',  'like',"%$search%")
                   ->orWhere('workgroups.name',    'like',"%$search%")
                   ->orWhere('facilities.name',    'like',"%$search%")
                   ->orWhere('task_types.title',   'like',"%$search%")
                   ->orWhere('schedule_types.title','like',"%$search%");
            }))
            ->select([
                DB::raw("'task' as record_type"),
                'tasks.id', 'tasks.display_id', 'tasks.status',
                'task_types.title as task_name',
                'schedule_types.title as schedule_name',
                'tasks.nextrun as due_at',
                'displays.manufacturer', 'displays.model', 'displays.serial',
                'displays.workstation_id',
                'workstations.name as ws_name',
                'workgroups.name as wg_name',
                'facilities.name as fac_name',
            ]);

        $qaRows = DB::table('qa_tasks')
            ->join('displays',    'qa_tasks.display_id',   '=', 'displays.id')
            ->join('workstations','displays.workstation_id','=', 'workstations.id')
            ->join('workgroups',  'workstations.workgroup_id','=','workgroups.id')
            ->join('facilities',  'workgroups.facility_id', '=', 'facilities.id')
            ->where('qa_tasks.deleted', 0)
            ->where('qa_tasks.nextdate', '>', 0)
            ->when($facility_id, fn($q) => $q->where('workgroups.facility_id', $facility_id))
            ->when($workgroup_id, fn($q) => $q->where('workgroups.id', $workgroup_id))
            ->when($workstation_id, fn($q) => $q->where('workstations.id', $workstation_id))
            ->when($display_id,  fn($q) => $q->where('qa_tasks.display_id', $display_id))
            ->when(!empty($display_ids), fn($q) => $q->whereIn('qa_tasks.display_id', $display_ids))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('displays.manufacturer','like',"%$search%")
                   ->orWhere('displays.model',     'like',"%$search%")
                   ->orWhere('displays.serial',    'like',"%$search%")
                   ->orWhere('workstations.name',  'like',"%$search%")
                   ->orWhere('workgroups.name',    'like',"%$search%")
                   ->orWhere('facilities.name',    'like',"%$search%")
                   ->orWhere('qa_tasks.name',      'like',"%$search%");
            }))
            ->select([
                DB::raw("'qa_task' as record_type"),
                'qa_tasks.id', 'qa_tasks.display_id',
                DB::raw('0 as status'),
                'qa_tasks.name as task_name',
                'qa_tasks.freq as schedule_name',
                'qa_tasks.nextdate as due_at',
                'displays.manufacturer', 'displays.model', 'displays.serial',
                'displays.workstation_id',
                'workstations.name as ws_name',
                'workgroups.name as wg_name',
                'facilities.name as fac_name',
            ]);

        $allRows = $taskRows->get()->merge($qaRows->get());
        $allRows = $sortMode === 'latest'
            ? $allRows->sortByDesc('id')
            : $allRows->sortBy('due_at');
        $total   = $allRows->count();
        $rows    = $allRows->slice($offset, $limit)->values();

        $data = $rows->map(function($r) {
            $ts = (int)$r->due_at;
            $dueFormatted = $ts ? \Carbon\Carbon::createFromTimestamp($ts)->format('d M Y H:i') : '-';
            $isPast = $ts && \Carbon\Carbon::createFromTimestamp($ts)->isPast();
            $isSoon = $ts && !$isPast && \Carbon\Carbon::createFromTimestamp($ts)->diffInDays(\Carbon\Carbon::now()) <= 7;
            $statusMap = [0 => 'OK', 1 => 'Failed', 2 => 'Run Error'];
            return [
                'id'          => $r->id,
                'type'        => $r->record_type,
                'displayId'   => $r->display_id,
                'displayName' => trim(($r->manufacturer ?? '') . ' ' . ($r->model ?? '')) . ' (' . ($r->serial ?? '') . ')',
                'wsName'      => $r->ws_name  ?? '-',
                'taskName'    => $r->task_name ?? $r->record_type,
                'scheduleName'=> $r->schedule_name ?? '-',
                'dueAt'       => $dueFormatted,
                'dueColor'    => $isPast ? 'danger' : ($isSoon ? 'warning' : 'success'),
                'status'      => $statusMap[$r->status] ?? 'Unknown',
                'statusColor' => $r->status == 0 ? 'success' : 'danger',
            ];
        });

        return response()->json(['data' => $data, 'total' => $total]);
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
        $limit  = (int) $request->get('limit', 25);
        $page   = (int) $request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $query = DB::table('tasks')
            ->join('displays', 'tasks.display_id', '=', 'displays.id')
            ->join('workstations', 'displays.workstation_id', '=', 'workstations.id')
            ->join('workgroups', 'workstations.workgroup_id', '=', 'workgroups.id')
            ->join('facilities', 'workgroups.facility_id', '=', 'facilities.id')
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
                'tasks.nextrun as due_at',
                'task_types.title as task_name',
                'schedule_types.title as schedule_name',
                'displays.manufacturer',
                'displays.model',
                'displays.serial',
                'workstations.name as ws_name',
                'workgroups.name as wg_name',
                'facilities.name as fac_name',
            ]);

        $total = $query->count();
        $rows = (clone $query)
            ->orderByDesc('tasks.id')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $data = $rows->map(function ($r) {
            $ts = (int) $r->due_at;
            return [
                'id' => $r->id,
                'displayId' => $r->display_id,
                'displayName' => trim(($r->manufacturer ?? '') . ' ' . ($r->model ?? '')) . ' (' . ($r->serial ?? '') . ')',
                'wsName' => $r->ws_name ?? '-',
                'wgName' => $r->wg_name ?? '-',
                'facName' => $r->fac_name ?? '-',
                'taskName' => $r->task_name ?: 'Calibration',
                'scheduleName' => $r->schedule_name ?: 'Manual',
                'dueAt' => $ts ? \Carbon\Carbon::createFromTimestamp($ts)->format('d M Y H:i') : '-',
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

        $facility_id = $user->facility_id;
        $userRole    = \App\Helpers\AuthHelper::getCurrentUserRole();
        $search = $request->get('search', '');
        $limit  = (int)$request->get('limit', 25);
        $page   = (int)$request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $query = DB::table('alerts')
            ->leftJoin('facilities', 'facilities.id', '=', 'alerts.facility_id')
            ->select(['alerts.id', 'alerts.email', 'alerts.actived', 'alerts.daily_report',
                      'alerts.facility_id', 'facilities.name as fac_name'])
            ->when($userRole === 'admin', fn($q) => $q->where('alerts.facility_id', $facility_id))
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
        $alert = \App\Models\Alert::findOrFail($id);
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

        $search = $request->get('search', '');
        $limit  = (int)$request->get('limit', 25);
        $page   = (int)$request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $query = DB::table('facilities')
            ->leftJoin(DB::raw('(SELECT facility_id, COUNT(*) as cnt FROM workgroups GROUP BY facility_id) wgc'),
                'wgc.facility_id', '=', 'facilities.id')
            ->leftJoin(DB::raw('(SELECT facility_id, COUNT(*) as cnt FROM users GROUP BY facility_id) uc'),
                'uc.facility_id', '=', 'facilities.id')
            ->leftJoin(DB::raw('(SELECT w2.facility_id, COUNT(d.id) as cnt FROM displays d JOIN workstations ws ON ws.id = d.workstation_id JOIN workgroups w2 ON w2.id = ws.workgroup_id GROUP BY w2.facility_id) dc'),
                'dc.facility_id', '=', 'facilities.id')
            ->select([
                'facilities.id', 'facilities.name', 'facilities.location', 'facilities.timezone',
                DB::raw('COALESCE(wgc.cnt, 0) as workgroups_count'),
                DB::raw('COALESCE(uc.cnt, 0) as users_count'),
                DB::raw('COALESCE(dc.cnt, 0) as displays_count'),
            ])
            ->when($role !== 'super', fn($q) => $q->where('facilities.id', $user->facility_id))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('facilities.name',     'like', "%$search%")
                   ->orWhere('facilities.timezone','like', "%$search%");
            }));

        $total = $query->count();
        $rows  = (clone $query)->orderBy('facilities.name')->offset($offset)->limit($limit)->get();

        $data = $rows->map(fn($r) => [
            'id'               => $r->id,
            'name'             => $r->name,
            'location'         => $r->location ?? '-',
            'timezone'         => $r->timezone ?? '-',
            'workgroupsCount'  => (int)$r->workgroups_count,
            'usersCount'       => (int)$r->users_count,
            'displaysCount'    => (int)$r->displays_count,
            'canManage'        => in_array($role, ['super', 'admin'], true),
            'canDelete'        => $role === 'super',
        ]);

        return response()->json(['data' => $data, 'total' => $total]);
    }
}
