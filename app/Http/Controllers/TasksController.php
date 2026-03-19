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
    public function edit_task(Request $request)
    {
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
            'request' => $request
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
        $id=$request->input('id');
        $this->validate($request, [
            'tasktype' => 'required',
            'scheduletype' => 'required'
        ]);
        $workstation_id=$request->input('workstation2');
        $workgroup_id=$request->input('workgroup2');

        if($id=='0')
        {
            if($request->input('displays')!='')
                $displayIds = explode(';', $request->input('displays'));
            elseif($workgroup_id==null AND $workstation_id==null)
                {
                    $facility_id=$request->input('facility2');
                    $items = \App\Models\Display::join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                    ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                    ->where('workgroups.facility_id', '=', $facility_id)
                    ->pluck('displays.id');
                    $displayIds=$items;
                }
            elseif($workstation_id==NULL)
            {
                $facility_id=$request->input('facility2');
                $workgroup_id=$request->input('workgroup2');
                $items = \App\Models\Display::join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                    ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                    ->where('workgroups.facility_id', '=', $facility_id)
                    ->where('workgroups.id', '=', $workgroup_id)
                    ->pluck('displays.id');
                $displayIds=$items;
            }
            else
            {
                $facility_id=$request->input('facility2');
                $workgroup_id=$request->input('workgroup2');
                $workstation_id=$request->input('workstation2');
                $items = \App\Models\Display::join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                    ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                    ->where('workgroups.facility_id', '=', $facility_id)
                    ->where('workgroups.id', '=', $workgroup_id)
                    ->where('workstations.id', '=', $workstation_id)
                    ->pluck('displays.id');
                $displayIds=$items;
            }

            foreach ($displayIds as $displayId) {
                $task = new Task();
                $task->display_id = $displayId;
                $this->setTask($task, $request);
                $dat=$request->input('startdate').' '.$request->input('starttime');
                $task->nextrun = Carbon::createFromFormat('Y-m-d H:i', $dat)->timestamp;
                $task->save();
            }
        }
        else
        {
            $task = Task::find($id);
            $this->setTask($task, $request);
            $task->save();
        }

        $data['success']=1;
        return response()->json($data);
    }

    public function delete_task(Request $request){
        $data=array();
        $data['success']=0;

        $id=$request->input('id');
        
        $item = \App\Models\Task::findOrFail($id);
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
