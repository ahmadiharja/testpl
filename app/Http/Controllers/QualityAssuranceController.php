<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class QualityAssuranceController extends Controller
{
    public function quality_assuarance(Request $request){
        
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role=$request->session()->get('role');
        
        $cacheKey = '';
        if ($role!='super') { // load current facility only
            $facilities = array($user->facility);
            //var_dump($facilities); exit();

        } else { // load all facilities
            $facilities = \App\Models\Facility::all();
        }
        
        //calibrate
        if($request->input('displays')!='')
        {
            $facility=$request->input('facility');
            $workgroup=$request->input('workgroup');
            $workstation=$request->input('workstation');
            $displays=$request->input('displays');
            
            //set_timezone();
            $date=date('Y-m-d');
            $time=date('H:i');
            $unixtime=now()->timestamp;
            
            foreach($displays as $display) {
                $d = \App\Models\Display::find($display);
                if ($d->preference('exclude')) continue;

                \App\Models\Task::create([
                    'display_id' => $display ,
                    'type' => 'cal',
                    'testpattern' => 'SMPTE',
                    'schtype' => 1,
                    'startdate' => Carbon::now()->timezone($user->facility->timezone)->format('Y.m.d'),
                    'starttime' => Carbon::now()->timezone($user->facility->timezone)->format('H:i'),
                    'status' => 0,
                    'nextrun' => $unixtime,
                    'user_id' => $user_id,
                    'sync' => 0,
                    'deleted' => 0,
                    'created_at'=>NOW()
                ]);
            }
            
            $request->session()->flash('success', "Calibration task created successfully!");
            return redirect('scheduler');
        }
        //shedule task calibrate
        /*if($request->input('displays2')!='')
        {
            $facility=$request->input('facility2');
            $workgroup=$request->input('workgroup2');
            $workstation=$request->input('workstation2');
            $displays=$request->input('displays2');
            
            //set_timezone();
            $date=date('Y-m-d');
            $time=date('H:i');
            $unixtime=now()->timestamp;
            
            foreach($displays as $display) {
                $d = \App\Models\Display::find($display);
                if ($d->preference('exclude')) continue;

                \App\Models\Task::create([
                    'display_id' => $display ,
                    'type' => 'cal',
                    'testpattern' => 'SMPTE',
                    'schtype' => 1,
                    'startdate' => Carbon::now()->timezone($user->facility->timezone)->format('Y.m.d'),
                    'starttime' => Carbon::now()->timezone($user->facility->timezone)->format('H:i'),
                    'status' => 0,
                    'nextrun' => $unixtime,
                    'user_id' => $user_id,
                    'sync' => 0,
                    'deleted' => 0,
                    'created_at'=>NOW()
                ]);
            }
            
            $request->session()->flash('success', "Schedule task created successfully!");
            return redirect('scheduler');
        }*/
        
        return view('scheduler.index', [
            'title'=> 'Scheduler',
            'facilities'=> $facilities,
            'role' => $role,
        ]);
    }
    
     public function fetch_workgroups2(Request $request)
    {
        $data=array();
        $data['success']=0;
        
        $data['content']="<option value=''>Please select</option>";
        
        $facility_id=$request->input('id');
        if($facility_id=='') $facility_id=0;
            
        $row=\App\Models\Workgroup::where('facility_id', $facility_id)->get();
       
        foreach($row as $r)
        {
            $data['content'].="<option value='".$r->id."'>".$r->name."</option>";
        }
       
        $data['success']=1;
        return response()->json($data);
    }
    
     
    public function fetch_workstations2(Request $request)
    {
        $data=array();
        $data['success']=0;
        
        $data['content']="<option value=''>Please select</option>";
        
        $workgroup_id=$request->input('id');
        if($workgroup_id=='') $workgroup_id=0;
        
        $row=\App\Models\Workstation::where('workgroup_id', $workgroup_id)->get();
        foreach($row as $r){
            $data['content'].="<option value='".$r->id."'>".$r->name."</option>";
        }
        
        $data['success']=1;
         return response()->json($data);
    }
    
     public function fetch_displays_checklist2(Request $request)
    {
        $data=array();
        $data['success']=0;
        
        $data['content']="<div class='form-check mb-0 py-1 px-4'>
        <input class='form-check-input flex-shrink-0' type='checkbox' id='' name='displays2[]'>
        <label class='form-check-label flex-grow-1' for='formCheck-7'>Select All</label>
        </div>";
        
        $workstation_id=$request->input('id');
        if($workstation_id=='') $workstation_id=0;
            
        $row=\App\Models\Display::where('workstation_id', $workstation_id)->get();
       
        foreach($row as $r)
        {
            $data['content'].="<div class='form-check mb-0 py-1 px-4'>
        <input class='form-check-input flex-shrink-0' type='checkbox' id='".$r->id."' value='".$r->id."' name='displays2[]'>
        <label class='form-check-label flex-grow-1' for='formCheck-7'>".$r->manufacturer." ".$r->model." (".$r->serial.")</label>
        </div>";
        }
       
        $data['success']=1;
        return response()->json($data);
    }
}
