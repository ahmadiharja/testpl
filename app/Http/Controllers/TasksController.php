<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\SettingsName;
use \App\Models\Task;
use \App\Models\ScheduleType;
use \App\Models\TestPattern;
use \App\Models\TaskType;
use Carbon\Carbon;

class TasksController extends Controller
{
    protected function taskManager(Request $request): ?\App\Models\User
    {
        $userId = $request->session()->get('id');

        return \App\Models\User::find($userId);
    }

    protected function canManageTasks(?\App\Models\User $user): bool
    {
        return $user && $user->hasAnyRole(['super', 'admin']);
    }

    protected function taskFacilityId(\App\Models\Task $task): ?int
    {
        return optional(optional(optional(optional($task->display)->workstation)->workgroup)->facility)->id;
    }

    protected function taskTargetIsAccessible(?\App\Models\User $user, \App\Models\Task $task): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->hasRole('super')) {
            return true;
        }

        return (int) $this->taskFacilityId($task) === (int) $user->facility_id;
    }

    protected function scopedDisplayIds(Request $request, ?\App\Models\User $user): array
    {
        $displayIds = [];
        $requestedDisplays = (string) $request->input('displays', '');
        $workstationId = $request->input('workstation2');
        $workgroupId = $request->input('workgroup2');
        $requestedFacilityId = $request->input('facility2');
        $facilityId = $user && !$user->hasRole('super')
            ? $user->facility_id
            : $requestedFacilityId;

        $query = \App\Models\Display::query()
            ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id');

        if ($requestedDisplays !== '') {
            $ids = collect(explode(';', $requestedDisplays))
                ->filter(fn ($item) => trim((string) $item) !== '')
                ->map(fn ($item) => (int) $item)
                ->values()
                ->all();

            $query->whereIn('displays.id', $ids);
        } else {
            if ($facilityId) {
                $query->where('workgroups.facility_id', '=', $facilityId);
            }

            if ($workgroupId) {
                $query->where('workgroups.id', '=', $workgroupId);
            }

            if ($workstationId) {
                $query->where('workstations.id', '=', $workstationId);
            }
        }

        if ($user && !$user->hasRole('super')) {
            $query->where('workgroups.facility_id', '=', $user->facility_id);
        }

        return $query->pluck('displays.id')->map(fn ($id) => (int) $id)->all();
    }

    protected function resolveTaskTimezone(?\App\Models\Task $task): string
    {
        return optional(optional(optional(optional($task?->display)->workstation)->workgroup)->facility)->timezone
            ?: config('app.timezone');
    }

    protected function computeTaskNextRunTimestamp(\App\Models\Task $task): int
    {
        $timezone = $this->resolveTaskTimezone($task);
        $startDate = str_replace('.', '-', (string) $task->startdate);
        $startTime = (string) ($task->starttime ?: '00:00');

        return Carbon::createFromFormat('Y-m-d H:i', trim($startDate . ' ' . $startTime), $timezone)
            ->utc()
            ->timestamp;
    }

    protected function taskRequestWantsJson(Request $request): bool
    {
        return $request->ajax() || $request->expectsJson();
    }

    protected function taskValidationFailure(Request $request, string $message, array $errors = [], int $status = 422)
    {
        if ($this->taskRequestWantsJson($request)) {
            return response()->json([
                'success' => 0,
                'message' => $message,
                'errors' => $errors ?: ['startdate' => [$message]],
            ], $status);
        }

        return back()->withErrors($errors ?: ['startdate' => $message])->withInput();
    }

    protected function validateStartDateNotPast(Request $request, array $displayIds = [], ?\App\Models\Task $task = null): ?string
    {
        $rawDate = trim((string) $request->input('startdate', ''));
        if ($rawDate === '') {
            return null;
        }

        $normalizedDate = str_replace('.', '-', $rawDate);
        try {
            Carbon::createFromFormat('Y-m-d', $normalizedDate);
        } catch (\Throwable $e) {
            return 'Start date is invalid.';
        }

        $timezones = collect();
        if ($task) {
            $timezones->push($this->resolveTaskTimezone($task));
        }

        if (!empty($displayIds)) {
            $displayTimezones = \App\Models\Display::query()
                ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
                ->whereIn('displays.id', $displayIds)
                ->pluck('facilities.timezone');
            $timezones = $timezones->merge($displayTimezones);
        }

        $timezones = $timezones
            ->filter()
            ->unique()
            ->values();

        if ($timezones->isEmpty()) {
            $timezones->push(config('app.timezone', 'UTC'));
        }

        foreach ($timezones as $timezone) {
            $selectedDate = Carbon::createFromFormat('Y-m-d', $normalizedDate, $timezone)->startOfDay();
            $today = Carbon::now($timezone)->startOfDay();
            if ($selectedDate->lt($today)) {
                return 'Start date cannot be in the past.';
            }
        }

        return null;
    }

    public function edit_task(Request $request)
    {
        $user = $this->taskManager($request);
        if (!$this->canManageTasks($user)) {
            return response()->json(['success' => 0, 'message' => 'You do not have access to manage scheduled tasks.'], 403);
        }

        $id=$request->input('id');
        if($id=='' OR $id==NULL) $id=0;
        $tasktype = TaskType::where('status', 1)->orderBy('id')->pluck('title', 'key')->toArray();
        $testpattern = TestPattern::where('status', 1)->orderBy('id')->pluck('title', 'value')->toArray();
        $scheduletype = ScheduleType::orderBy('id')->pluck('title', 'client_id')->toArray();

        $dayofweek = SettingsName::wheresetting_name('DaysOfWeek')->first();
        if ($dayofweek) {
            $dayofweek = json_decode($dayofweek['setting_value'], true);
        } else {
            $dayofweek = json_decode('{"1":"Monday","2":"Tuesday","3":"Wednesday","4":"Thursday","5":"Friday","6":"Saturday","7":"Sunday"}');
        }


        $weekly = SettingsName::wheresetting_name('WeekOfMonth')->first();
        if ($weekly) {
            $weekly = json_decode($weekly['setting_value'], true);
        } else {
            $weekly = json_decode('{"1":"First","2":"Second","3":"Third","4":"Fourth","5":"Last"}');
        }



        $monthly = SettingsName::wheresetting_name('Monthes')->first();
        if ($monthly) {
            $monthly = json_decode($monthly['setting_value'], true);
        } else {
            $monthly = json_decode('{"1":"January","2":"February","3":"March","4":"April","5":"May","6":"June","7":"July","8":"August","9":"September","10":"October","11":"November","12":"December"}');
        }


        // $task = Task::with('display')->find($id);
        $task = Task::With(['Display'])->find($id);
        if ($task && !$this->taskTargetIsAccessible($user, $task)) {
            return response()->json(['success' => 0, 'message' => 'You do not have access to update this task.'], 403);
        }
        if(!isset($task->id))
        {
            $task=new Task;
            $task->id=0;
            $task->type='cal';
            $task->schtype='0';
            $task->testpattern='SMPTE';
            $task->startdate=date('Y-m-d');
            $task->starttime=date('H:i');
        }

        $displays='';
        if($request->input('displays')!='')
        $displays=implode(';', $request->input('displays'));

        $data2 = array(
            'tasktype' => $tasktype,
            'testpattern' => $testpattern,
            'scheduletype' => $scheduletype,
            'weekly' => $weekly,
            'dayofweek' => $dayofweek,
            'monthly' => $monthly,
            'task' => $task,
            'displays' => $displays,
            'request' => $request,
            'quickCalibration' => $request->boolean('quick_calibration'),
            'lockTaskType' => $request->boolean('lock_tasktype') || $request->boolean('quick_calibration'),
            'rescheduleLite' => $request->boolean('reschedule_lite'),
            'minStartDate' => Carbon::now(config('app.timezone', 'UTC'))->format('Y-m-d'),
        );

        $data=array();
        $data['content']=view("tasks.edit")->with($data2)->render();
        $data['success']=1;
        return response()->json($data);
    }

    public function update_task(Request $request)
    {
        $data=array();
        $data['success']=0;
        $user = $this->taskManager($request);

        if (!$this->canManageTasks($user)) {
            return response()->json(['success' => 0, 'message' => 'You do not have access to manage scheduled tasks.'], 403);
        }

        $id=$request->input('id');
        $this->validate($request, [
            'tasktype' => 'required',
            'scheduletype' => 'required'
        ]);

        if($id=='0')
        {
            $requestedDisplays = collect(explode(';', (string) $request->input('displays', '')))
                ->filter(fn ($item) => trim((string) $item) !== '')
                ->map(fn ($item) => (int) $item)
                ->values();
            $displayIds = $this->scopedDisplayIds($request, $user);

            if ($requestedDisplays->isNotEmpty() && $requestedDisplays->count() !== count($displayIds)) {
                return response()->json([
                    'success' => 0,
                    'message' => 'One or more selected displays are outside your allowed scope.',
                ], 403);
            }

            if (count($displayIds) === 0) {
                return response()->json([
                    'success' => 0,
                    'message' => 'No displays were found within your allowed scope.',
                ], 422);
            }

            if ($message = $this->validateStartDateNotPast($request, $displayIds)) {
                return $this->taskValidationFailure($request, $message);
            }

            foreach ($displayIds as $displayId) {
                $task = new Task();
                $task->display_id = $displayId;
                $task->user_id = $user?->id;
                $this->setTask($task, $request);
                $task->nextrun = $this->computeTaskNextRunTimestamp($task);
                $task->created_at = now();
                $task->updated_at = now();
                $task->save();
            }
        }
        else
        {
            $task = Task::find($id);
            if (!$task) {
                return response()->json(['success' => 0, 'message' => 'Task not found.'], 404);
            }

            if (!$this->taskTargetIsAccessible($user, $task)) {
                return response()->json(['success' => 0, 'message' => 'You do not have access to update this task.'], 403);
            }

            if ($message = $this->validateStartDateNotPast($request, [], $task)) {
                return $this->taskValidationFailure($request, $message);
            }

            $this->setTask($task, $request);
            $task->nextrun = $this->computeTaskNextRunTimestamp($task);
            $task->updated_at = now();
            $task->save();
        }

        $data['success']=1;
        if (!$this->taskRequestWantsJson($request)) {
            return back()->with('success', 'Task saved successfully.');
        }

        return response()->json($data);
    }

    public function delete_task(Request $request){
        $data=array();
        $data['success']=0;
        $user = $this->taskManager($request);

        if (!$this->canManageTasks($user)) {
            return response()->json(['success' => 0, 'msg' => 'You do not have access to manage scheduled tasks.'], 403);
        }

        $id=$request->input('id');
        
        $item = \App\Models\Task::findOrFail($id);
        if (!$this->taskTargetIsAccessible($user, $item)) {
            return response()->json(['success' => 0, 'msg' => 'You do not have access to delete this task.'], 403);
        }
        $item->deleted = 1;
        $item->sync = 0;
        $item->save();

        $data['msg']="Task deleted successfully!";
        $data['success']=1;

        return response()->json($data);
        
    }

    private function setTask($task, $request)
    {
        $user_id=$request->session()->get('id');
        $timezone = $task->display->workstation->workgroup->facility->timezone;

        $task->type = $request->input('tasktype');
        $task->testpattern = $request->input('testpattern') ? $request->input('testpattern') : 'SMPTE';
        $task->schtype = $request->input('scheduletype');
        $task->disabled = $request->input('disabletask') ? 1 : 0;
        $task->sync = 0;
        $task->deleted = 0;

        $task->starttime = $request->input('starttime') ? $request->input('starttime') : Carbon::now($timezone)->format('H:i');
        $task->startdate = $request->input('startdate') ? $request->input('startdate') : Carbon::now($timezone)->format('Y-m-d');
        $task->setDayOption($request);

        return true;
    }
}
