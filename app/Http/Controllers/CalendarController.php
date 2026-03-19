<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\QATask;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    public function index()
    {
        return view("calendar.index");
    }

    public function events(Request $request) {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);

        $role=$request->session()->get('role');

        // get timezone
        $timezone = $user->timezone;
        $start = Carbon::parse($request->input('start'),  $timezone)->getTimestamp();
        $end = Carbon::parse($request->input('end'),  $timezone)->getTimestamp();

        $res = [];
        if ($role!='super') {
            //$tasksNextrun = auth()->user()->facility->tasks()->whereBetween('nextrun', [$start, $end])->whereNotIn('schtype', [2])->get();
            $tasksNextrun = $user->facility->tasks()->has('display')->whereBetween('nextrun', [$start, $end])->get();
        } else {
            //$tasksNextrun = Task::whereBetween('nextrun', [$start, $end])->whereNotIn('schtype', [2])->get();
            $tasksNextrun = Task::has('display')->whereBetween('nextrun', [$start, $end])->get();
        }
        $tasksNextrun = $tasksNextrun->filter(function($task) {
            return $task->display->preference('exclude')==0;
        });
        foreach ($tasksNextrun as $task) {
            if($task->taskType==NULL) Log::info('TaskType NULL for Task ID: '.$task->id);
            $data = [
                'display' => $task->display->serial,
                'workstation' => $task->display->workstation->name,
                'workgroup' => $task->display->workstation->workgroup->name,
                'facility' => $task->display->workstation->workgroup->facility->name,
                'tasktype' => $task->taskType->title,
                'testpattern' => $task->testPattern->title,
                'schtype' => $task->scheduleType->title,
                'startdate' => $task->startdate,
                'lastrun' => $task->lastrun,
                'nextrun' => $task->getNextrunString(),
                'disabled' => $task->disabledText,
                'status' => $task->statusText,
                'taskid' => $task->id,
                'isqa' => 0

            ];
            if ($task->display && $task->display->preference('exclude') ) continue;
            $ws_info = $user->hasRole('super')?"\n(".$task->display->workstation->name."/".$task->display->model.")":'';
            $res[] = [
                'id' => 'task_next_' . $task->id,
                'title' => $task->taskType->title.$ws_info,
                'start' => $task->getNextrunString(),
                'allDay' => false,
                'editable' => false,
                //'url' => '/tasks/'.$task->id,
                'className'=> 'event-blue',
                'backgroundColor' => 'green',
                'data' => $data,
                'className' => $task->statusEventColor,
            ];
        }

        /*if (auth()->user()->hasRole('admin')) {
            $tasksLastrun = auth()->user()->facility->tasks()->whereBetween('lastrun', [date($start), date($end)])->whereNotIn('schtype', [2])->get();
        } else {
            $tasksLastrun = Task::whereBetween('lastrun', [date($start), date($end)])->whereNotIn('schtype', [2])->get();
        }
        
        foreach ($tasksLastrun as $task) {
            $data = [
                'display' => $task->display->serial,
                'workstation' => $task->display->workstation->name,
                'workgroup' => $task->display->workstation->workgroup->name,
                'facility' => $task->display->workstation->workgroup->facility->name,
                'tasktype' => $task->taskType->title,
                'testpattern' => $task->testPattern->title,
                'schtype' => $task->scheduleType->title,
                'startdate' => $task->startdate,
                'lastrun' => $task->lastrun,
                'nextrun' => $task->nextrun,
                'disabled' => $task->disabledText,
                'status' => $task->statusText,


            ];
            $res[] = [
                'id' => 'task_last_' . $task->id,
                'title' => $task->taskType->title,
                'start' => $task->lastrun,
                'allDay' => false,
                'editable' => false,
                'url' => '/tasks/'.$task->id,
                'className'=> 'event-blue',
                'display' => $task->display->serial,
                'data' => $data,
            ];
        }*/

        // Get QA Tasks
        if ($role!='super') {
            $qatasks = $user->facility->qatasks()->has('display')->whereBetween('nextdate', [$start, $end])->get();
        } else {
            $qatasks = QATask::has('display')->whereBetween('nextdate', [$start, $end])->get();
        }
        $qatasks = $qatasks->filter(function($task) {
            return $task->display->preference('exclude')==0;
        });
        foreach ($qatasks as $task) {
            $data = [
                'display' => $task->display->serial,
                'workstation' => $task->display->workstation->name,
                'workgroup' => $task->display->workstation->workgroup->name,
                'facility' => $task->display->workstation->workgroup->facility->name,
                'tasktype' => $task->name,
                'testpattern' => '',
                'schtype' => $task->freq,
                'startdate' => Carbon::createFromTimeStamp($task->nextdate, $timezone)->format('Y-m-d'),
                'lastrun' => $task->lastrundate,
                'nextrun' => $task->nextrun,
                'disabled' => $task->disabledText,
                'status' => $task->statusText,
                'taskid' => $task->id,
                'isqa' => 1
            ];
            $res[] = [
                'id' => 'qa_task_' . $task->id,
                'title' => $task->name,
                'start' => Carbon::createFromTimeStamp($task->nextdate, $timezone)->format('Y-m-d'),
                'allDay' => true,
                'editable' => false,
               // 'url' => '/qatasks/'.$task->id,
                'className'=> 'event-blue',
                'backgroundColor' => 'blue',
                'display' => $task->display->serial,
                'data' => $data,
            ];
        }

        return $res;
    }
}
