<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class DisplaysController extends Controller
{
    protected function display_can_manage(?string $role): bool
    {
        return in_array($role, ['super', 'admin'], true);
    }

    protected function resolveAuthorizedDisplay(Request $request, $displayId, bool $mustManage = false): \App\Models\Display
    {
        $displayId = (int) str_replace('di-', '', (string) $displayId);
        $user = \App\Models\User::find($request->session()->get('id'));
        $userRole = $request->session()->get('role');

        $display = \App\Models\Display::with(['preferences', 'workstation.workgroup.facility'])->findOrFail($displayId);
        $facility = optional(optional($display->workstation)->workgroup)->facility;

        if ($userRole !== 'super' && (!$user || !$facility || (int) $facility->id !== (int) $user->facility_id)) {
            abort(404);
        }

        if ($mustManage && !$this->display_can_manage($userRole)) {
            abort(403);
        }

        return $display;
    }

    protected function createImmediateCalibrationTask(\App\Models\Display $display, int $userId): \App\Models\Task
    {
        $timezone = optional(optional(optional($display->workstation)->workgroup)->facility)->timezone ?: config('app.timezone');
        $now = Carbon::now($timezone);

        return \App\Models\Task::create([
            'display_id' => $display->id,
            'type' => 'cal',
            'testpattern' => 'SMPTE',
            'schtype' => 1,
            'startdate' => $now->format('Y.m.d'),
            'starttime' => $now->format('H:i'),
            'status' => 0,
            'nextrun' => $now->timestamp,
            'user_id' => $userId,
            'sync' => 0,
            'deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function displays(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role = $request->session()->get('role');

        $facilities = \App\Models\Facility::query()
            ->when($role !== 'super', function ($query) use ($user) {
                return $query->where('id', $user->facility_id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        $workgroupsByFacility = \App\Models\Workgroup::query()
            ->when($role !== 'super', function ($query) use ($user) {
                return $query->where('facility_id', $user->facility_id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'facility_id'])
            ->groupBy('facility_id')
            ->map(function ($items) {
                return $items->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])->values();
            });

        $workstationsByWorkgroup = \App\Models\Workstation::query()
            ->when($role !== 'super', function ($query) use ($user) {
                return $query->whereHas('workgroup', function ($groupQuery) use ($user) {
                    $groupQuery->where('facility_id', $user->facility_id);
                });
            })
            ->orderBy('name')
            ->get(['id', 'name', 'workgroup_id'])
            ->groupBy('workgroup_id')
            ->map(function ($items) {
                return $items->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])->values();
            });

        return view('displays.displays', [
            'title'=>'Displays',
            'filters' => [
                'canChooseFacility' => $role === 'super',
                'facilities' => $facilities->map(fn ($facility) => [
                    'id' => $facility->id,
                    'name' => $facility->name,
                ])->values(),
                'workgroupsByFacility' => $workgroupsByFacility,
                'workstationsByWorkgroup' => $workstationsByWorkgroup,
                'selectedFacilityId' => $role === 'super'
                    ? (string) $request->get('facility_id', '')
                    : (string) $user->facility_id,
                'selectedWorkgroupId' => (string) $request->get('workgroup_id', ''),
                'selectedWorkstationId' => (string) $request->get('workstation_id', ''),
            ],
        ]);
    }

    public function fetch_workgroups(Request $request)
    {
        $data=array();
        $data['success']=0;
        $user = \App\Models\User::find($request->session()->get('id'));
        $role = $request->session()->get('role');
        
        $data['content']="<option value=''>Select facility first</option>";
        
        $facility_id=$request->input('id');
        if($facility_id=='') $facility_id=0;
        if ($role !== 'super') {
            $facility_id = (int) optional($user)->facility_id;
        }
            
        $row=\App\Models\Workgroup::where('facility_id', $facility_id)->get();
        if(count($row)!=0) $data['content']="<option value=''>Please select</option>";
       
        foreach($row as $r)
        {
            $data['content'].="<option value='".$r->id."'>".$r->name."</option>";
        }
       
        $data['success']=1;
        return response()->json($data);
    }
    
    public function fetch_workstations(Request $request)
    {
        $data=array();
        $data['success']=0;
        $user = \App\Models\User::find($request->session()->get('id'));
        $role = $request->session()->get('role');
        
        $data['content']="<option value=''>Please select</option>";
        
        $workgroup_id=$request->input('id');
        if($workgroup_id=='') $workgroup_id=0;

        if ($role !== 'super') {
            $allowed = \App\Models\Workgroup::where('id', $workgroup_id)
                ->where('facility_id', optional($user)->facility_id)
                ->exists();

            if (!$allowed) {
                return response()->json(['success' => 0, 'content' => "<option value=''>Please select</option>"], 403);
            }
        }
        
        $row=\App\Models\Workstation::where('workgroup_id', $workgroup_id)->get();
        foreach($row as $r){
            $data['content'].="<option value='".$r->id."'>".$r->name."</option>";
        }
        
        $data['success']=1;
         return response()->json($data);
    }
    
    public function fetch_displays(Request $request)
    {
        $data=array();
        $data['success']=0;
        $user = \App\Models\User::find($request->session()->get('id'));
        $role = $request->session()->get('role');
        
        $data['content']="<option value=''>Please select</option>";
        
        $workstation_id=$request->input('id');
        if($workstation_id=='') $workstation_id=0;

        if ($role !== 'super') {
            $allowed = \App\Models\Workstation::where('workstations.id', $workstation_id)
                ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                ->where('workgroups.facility_id', optional($user)->facility_id)
                ->exists();

            if (!$allowed) {
                return response()->json(['success' => 0, 'content' => "<option value=''>Please select</option>"], 403);
            }
        }
            
        $row=\App\Models\Display::where('workstation_id', $workstation_id)->get();
       
        foreach($row as $r)
        {
            $data['content'].="<option value='".$r->id."'>".$r->manufacturer." ".$r->model." (".$r->serial.")</option>";
        }
       
        $data['success']=1;
        return response()->json($data);
        
    }
    
    public function delete_display(Request $request)
    {
        $data=array();
        $data['msg']='';
        $data['success']=0;
        $user_id=$request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        $userRole = $request->session()->get('role');

        if (!$this->display_can_manage($userRole)) {
            return response()->json(['success' => 0, 'msg' => 'You are not allowed to delete this display.'], 403);
        }

        $delete_id=$request->input('id');
        $display = \App\Models\Display::with('workstation.workgroup.facility')->find($delete_id);
        if (!$display) {
            return response()->json(['success' => 0, 'msg' => 'Display not found.'], 404);
        }
        $facility = optional(optional($display->workstation)->workgroup)->facility;
        if ($userRole !== 'super' && (!$user || !$facility || (int) $facility->id !== (int) $user->facility_id)) {
            return response()->json(['success' => 0, 'msg' => 'Display not found.'], 404);
        }

        $display->delete();

        $data['success']=1;
        $data['msg']='Display deleted successfully!';
        return response()->json($data);
    }
    
    public function display_calibration(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $userRole=$request->session()->get('role');

        abort_unless($this->display_can_manage($userRole), 403);

        //$workgroups_ids=\App\Models\Workgroup::where('user_id', $user_id)->pluck('id');
        //$facility_ids=\App\Models\Workgroup::whereIn('id', $workgroups_ids)->pluck('facility_id');
        //$facilities=\App\Models\Facility::whereIn('id', $facility_ids)->pluck('name','id');

        $cacheKey = '';
        if ($userRole!='super') { // load current facility only
            $facilities = array($user->facility);
            //var_dump($facilities); exit();

        } else { // load all facilities
            $facilities = \App\Models\Facility::all();
        }

        $calibrationScope = \App\Models\Task::query()
            ->join('displays', 'tasks.display_id', '=', 'displays.id')
            ->join('workstations', 'displays.workstation_id', '=', 'workstations.id')
            ->join('workgroups', 'workstations.workgroup_id', '=', 'workgroups.id')
            ->where('tasks.type', 'cal')
            ->where('tasks.deleted', 0)
            ->where('tasks.disabled', 0)
            ->where('tasks.nextrun', '>', 0)
            ->when($userRole !== 'super', function ($query) use ($user) {
                return $query->where('workgroups.facility_id', $user->facility_id);
            });

        $nowTs = now()->timestamp;
        $calibrationStats = [
            'activeJobs' => (clone $calibrationScope)->count(),
            'dueSoon' => (clone $calibrationScope)->whereBetween('tasks.nextrun', [$nowTs, $nowTs + (7 * 24 * 60 * 60)])->count(),
            'createdRecently' => (clone $calibrationScope)->whereNotNull('tasks.created_at')->where('tasks.created_at', '>=', now()->subDays(7))->count(),
            'scopeLabel' => $userRole === 'super' ? 'All facilities' : optional($user->facility)->name,
        ];
        
        
        if($request->input('calibrate')!='')
        {
            $facility=$userRole === 'super' ? $request->input('facility') : $user->facility_id;
            $workgroup=$request->input('workgroup');
            $workstation=$request->input('workstation');
            $displays=$request->input('displays');
            
            //set_timezone();
            $date=date('Y-m-d');
            $time=date('H:i');
            $unixtime=now()->timestamp;

            if($request->input('displays')==null)
            {
                if($workgroup==null AND $workstation==null)
                {
                    /*$workgroups=\App\Models\Workgroup::where('facility_id', $facility)->select('id')->get();
                    foreach($workgroups as $r2)
                    {
                        $workstations=\App\Models\Workstation::where('workgroup_id', $r2->id)->select('id')->get();
                        foreach($workgroups as $r3)
                        {
                            $displays=\App\Models\Display::where('workstation_id', $r3->id)->select('id')->get();
                        }
                    }*/
                    $facility_id=$facility;
                    $items = \App\Models\Display::join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                    ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                    ->where('workgroups.facility_id', '=', $facility_id)
                    ->pluck('displays.id');
                    $displays=$items;
                }
                elseif($workstation==null)
                {
                    $facility_id=$facility;
                    $workgroup_id=$workgroup;
                    $items = \App\Models\Display::join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                    ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                    ->where('workgroups.facility_id', '=', $facility_id)
                    ->where('workgroups.id', '=', $workgroup_id)  // Filter by workgroup_id
                    ->pluck('displays.id');
                    $displays=$items;
                }
                else
                {
                    $facility_id=$facility;
                    $workgroup_id=$workgroup;
                    $workstation_id=$workstation;
                    $items = \App\Models\Display::join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                    ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                    ->where('workgroups.facility_id', '=', $facility_id)
                    ->where('workgroups.id', '=', $workgroup_id)   // Filter by workgroup_id
                    ->where('workstations.id', '=', $workstation_id)  // Filter by workstation_id
                    ->pluck('displays.id');
                    $displays=$items;
                }
            }
            else {
                $selectedDisplays = \App\Models\Display::query()
                    ->whereIn('displays.id', (array) $displays)
                    ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                    ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                    ->when($userRole !== 'super', function ($query) use ($user) {
                        return $query->where('workgroups.facility_id', '=', $user->facility_id);
                    })
                    ->pluck('displays.id')
                    ->toArray();

                abort_if(count($selectedDisplays) !== count((array) $displays), 403);
                $displays = $selectedDisplays;
            }
            
            foreach($displays as $display) {
                $d = \App\Models\Display::find($display);
                if ($d->preference('exclude')) continue;

                if ($userRole!='super') {
                    $timezone=$user->facility->timezone;
                }
                else $timezone=$d->workstation->workgroup->facility->timezone;

                \App\Models\Task::create([
                    'display_id' => $display ,
                    'type' => 'cal',
                    'testpattern' => 'SMPTE',
                    'schtype' => 1,
                    'startdate' => Carbon::now()->timezone($timezone)->format('Y.m.d'),
                    'starttime' => Carbon::now()->timezone($timezone)->format('H:i'),
                    'status' => 0,
                    'nextrun' => $unixtime,
                    'user_id' => $user_id,
                    'sync' => 0,
                    'deleted' => 0,
                    'created_at'=>NOW()
                ]);
            }
            
            $request->session()->flash('success', "Calibration task created successfully!");
            return redirect()->route('displays.calibration');
        }

        if($request->input('tasktype')!='')
        {
            $tasktype=$request->input('tasktype');
            $scheduletype=$request->input('scheduletype');
            $disabletask=$request->input('disabletask');
            $starttime=$request->input('starttime');
            $dailytask=$request->input('dailytask');
            $dayinmonth=$request->input('dayinmonth');
            $week=$request->input('week');
            $rdayinmonth=$request->input('rdayinmonth');
            $dayofmonth=$request->input('dayofmonth');
            $week_of_month=$request->input('week_of_month');
            $monthly=$request->input('monthly');
            $weekdays=$request->input('weekdays');
            $startdate=$request->input('startdate');
            
            //set_timezone();
            $date=date('Y-m-d');
            $time=date('H:i');
            $unixtime=now()->timestamp;
            
            foreach($displays as $display) {
                \App\Models\Task::create([
                    'display_id' => $display ,
                    'type' => 'cal',
                    'testpattern' => 'SMPTE',
                    'schtype' => 1,
                    'startdate' => $date,
                    'starttime' => $time,
                    'status' => 0,
                    'nextrun' => $unixtime,
                    'user_id' => $user_id,
                    'sync' => 0,
                    'deleted' => 0,
                    'created_at'=>NOW()
                ]);
            }
            
            $request->session()->flash('success', "Calibration task created successfully!");
            return redirect()->route('displays.calibration');
        }
        
        return view('display_callibration.display_calibration', [
            'title'=>'Calibrate Display',
            'facilities'=>$facilities,
            'calibrationStats' => $calibrationStats,
        ]);
    }

    public function quick_calibrate_display(Request $request, $id)
    {
        $display = $this->resolveAuthorizedDisplay($request, $id, true);

        if ($display->preference('exclude')) {
            return response()->json(['message' => 'This display is excluded from calibration tasks.'], 422);
        }

        $task = $this->createImmediateCalibrationTask($display, (int) $request->session()->get('id'));

        return response()->json([
            'message' => 'Calibration task created successfully.',
            'taskId' => $task->id,
            'dueAt' => Carbon::createFromTimestamp((int) $task->nextrun)->format('d M Y H:i'),
        ]);
    }
    
    public function fetch_displays_checklist(Request $request)
    {
        $data=array();
        $data['success']=0;
        $user = \App\Models\User::find($request->session()->get('id'));
        $role = $request->session()->get('role');
        
        $data['content']="<div class='form-check mb-0 py-1 px-4'>
        <input class='form-check-input flex-shrink-0' type='checkbox' id='formCheck-7' name='displays[]'>
        <label class='form-check-label flex-grow-1' for='formCheck-7'>Select All</label>
        </div>";
        $data['content']='';
        
        $workstation_id=$request->input('id');
        if($workstation_id=='') $workstation_id=0;

        if ($role !== 'super') {
            $allowed = \App\Models\Workstation::where('workstations.id', $workstation_id)
                ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                ->where('workgroups.facility_id', optional($user)->facility_id)
                ->exists();

            if (!$allowed) {
                return response()->json(['success' => 0, 'content' => ''], 403);
            }
        }
            
        $row=\App\Models\Display::where('workstation_id', $workstation_id)->get();
       
        foreach($row as $r)
        {
            $data['content'].="<div class='form-check mb-0 py-1 px-4'>
        <input class='form-check-input flex-shrink-0 displays-check' type='checkbox' id='display-".$r->id."' value='".$r->id."' name='displays[]'>
        <label class='form-check-label flex-grow-1' for='display-".$r->id."'>".$r->manufacturer." ".$r->model." (".$r->serial.")</label>
        </div>";
        }

        //if(count($row)==0) $data['content']='';
       
        $data['success']=1;
        return response()->json($data);
    }
    
    public function display_settings(Request $request, $display_id)
    {
        $user = \App\Models\User::find($request->session()->get('id'));
        $userRole=$request->session()->get('role');
        $display = $this->resolveAuthorizedDisplay($request, $display_id, true);
        
        $workstation_app=$request->input('workstation_app');
        $leaf = 'di';
        if (strtoupper($workstation_app)=='ALL') $workstation_app = '';
        
        //$workgroup_ids=\App\Models\Workgroup::where('user_id', $user_id)->pluck('id');
        //$facilities_ids=\App\Models\Workgroup::whereIn('id', $workgroup_ids)->pluck('facility_id');
        //$facilities=\App\Models\Facility::whereIn('id', $facilities_ids)->select('id', 'name')->get();
        /*$settings=\App\Models\SettingName::pluck('setting_value', 'setting_name')->toArray();
        $display_tech=json_decode($settings['DisplayTechnology'], 1);
        foreach($display_tech as $value)
        {
            echo $value;
            echo '<br>';
        }
        exit();*/
        $workstation_id = $display->workstation_id;
        $workgroup_id = optional($display->workstation)->workgroup_id;
        $facility_id = optional(optional($display->workstation)->workgroup)->facility_id;
        
        //$workgroups=\App\Models\Workgroup::where('facility_id', $facility_id)->get();
        //$workstations=\App\Models\Workstation::where('workgroup_id', $workgroup_id)->get();
        
        /*if ($userRole!='super') {
            // load user facility only
            $facilities_main = array($user->facility);

        } else {
            // load all facilities
            $facilities_main = \App\Models\Facility::all();
        }*/
        
        $query = \App\Models\Facility::query();

        if ($userRole !== 'super') {
            $query->where('id', $user->facility_id);
        }

        $facilities = $query
            ->with([
                'workgroups.workstations.displays' => function ($q) use ($workstation_app, $leaf) {

                if ($workstation_app) {
                    $q->whereHas('workstation', function ($ws) use ($workstation_app) {
                        $ws->where('app', 'LIKE', "{$workstation_app}%");
                    });
                }

                if ($leaf === 'di') {
                    $q->whereHas('preferences', function ($p) {
                        $p->where('name', 'exclude')
                        ->where('value', '0');
                    });
                }
            }
            ])
        ->get();
        
        $workgroups   = $facilities->pluck('workgroups')->flatten();
        $workstations = $workgroups->pluck('workstations')->flatten();
        $displays     = $workstations->pluck('displays')->flatten();
        
        //echo count($displays); exit();
        
        /*$facilities = array(); $workgroups=array(); $workstations=array(); $displays=array();
        foreach ($facilities_main as $f)
        {
            $facilities[]=$f;
            
            // Load workgroups
            foreach ($f->workgroups as $wg)
            {
                $workgroups[] = $wg;
                
                // Load workstations
                //$workstations = $wg->workstations()->where('app', 'LIKE', "{$workstation_app}%")->get();
                $workstations2 = $wg->workstations()->get();
                foreach ($workstations2 as $ws) {
                    $workstations[] = $ws;

                    // Load displays
                    if ($leaf == 'di') {
                        foreach ($ws->displays as $d) {
                            //$exclude = $d->preference('exclude');
                            $displays[]=$d;
                        }
                    }
                }

                
            }   
        }
        echo count($facilities); exit();*/
        
        return view('displays.display_settings', ['title'=>'Display Settings', 'workgroups'=>$workgroups, 'workstations'=>$workstations, 'facilities'=>$facilities, 'display_id'=>$display_id, 'facility_id'=>$facility_id, 'workgroup_id'=>$workgroup_id, 'workstation_id'=>$workstation_id]);
    }

    public function load_display_settings(Request $request, $id)
    {
        $displayId = str_replace('di-', '', (string) $request->input('id', $id));
        $d = $this->resolveAuthorizedDisplay($request, $displayId, true);
        $displays = $d->preferences;
        $w = $d->workstation;
        
        $data = [];
        $options = [];

        // get all display by display_id
        foreach($displays as $dis){
            $data[$dis->name] = $dis->value;
        }
        
        // add information into Financial Status
        $data['purchase_date'] = $d->purchase_date;
        $data['initial_value'] = $d->initial_value;
        $data['expected_value'] = $d->expected_value;
        $data['annual_straight_line'] = $d->annual_straight_line;
        $data['monthly_straight_line'] = $d->monthly_straight_line;
        $data['current_value'] = $d->current_value;
        $data['expected_replacement_date'] = $d->expected_replacement_date;
        $data['treeText'] = $d->treeText;
        // $data['IgnoreDisplay'] = $d->active;
        
        $setting = $w->settings_names()->whereIn('setting_name', ['TypeOfDisplay','DisplayTechnology','ScreenSize','BacklightStabilization'])->get();
        if ($setting) {
             foreach($setting as $s)
                $options[$s->setting_name] = $s->setting_value;
        }
        // get lut_names
        $lut_names = $d->preference('lut_names');
        if ($lut_names != 'N/A') {
            $a = explode('||', $lut_names);
        } else {
            $a = array();
        }
        $options['lut_names'] = json_encode($a);


        // 2019-01-13 convert InstalationDate to yyyy-mm-dd
        $data['InstalationDate'] = str_replace('.','-',$data['InstalationDate']);
        //  dd($data);
        return ['data' => $data, 'options' => $options];
    }

    public function save_display_settings(Request $request, $id)
    {
        $input = $request->except(['_token','ajax','id']);
        
        // DB::beginTransaction();
        $display = $this->resolveAuthorizedDisplay($request, $id, true);
        $id = $display->id;
        
        //update active in Display table
        $input['exclude'] = isset($input['exclude']) ? 1 : 0;
       

        // if uncheck CommunicateType
        if(!isset($input['CommunicationType'])){
            $input['CommunicationType'] = 3;
        }

        if(!isset($input['InternalSensor'])){
            $input['InternalSensor'] = "false";
        }
        else {
            $input['InternalSensor'] = "true";
        }

        foreach($input as $key => $data){
            //
            $d = \App\Models\DisplayPreference::where('display_id',[$id])->where('name',[$key])->first();
            // only save if changed
            if ($d == null){
                $d = \App\Models\DisplayPreference::create([
                    'name' => $key,
                    'value' => $data,
                    'display_id' => $id,
                    'sync' => 1
                ]);
            }
            if($d && $d->value != $data){
                $d->value = $data;
                $d->sync = 0;
                $d->save();
            }
        }
        

        //$facility = auth()->user()->facility;
        //activity()->by($facility)->performedOn($d)->withProperties(['key'=>'edited', 'user_id' => auth()->user()->id])->log('Display updated by : '. auth()->user()->name);

        return $d;
        
    }

    public function save_display_fn(Request $request, $id)
    {
        // $this->validate($request,[
        //     'initial_value'=> 'numeric|nullable',
        //     'expected_value' => 'numeric|nullable',
        //     'annual_straight_line' => 'numeric|nullable',
        //     'monthly_straight_line' => 'numeric|nullable',
        //     'current_value ' => 'numeric|nullable',
        //     'purchase_date' =>'date',
        //     'expected_replacement_date' =>'date'
        // ]);

        $di = $this->resolveAuthorizedDisplay($request, $id, true);
        if($di){
            $di->purchase_date = $request->input('purchase_date');
            $di->initial_value = $request->input('initial_value');
            $di->expected_value = $request->input('expected_value');
            $di->annual_straight_line = $request->input('annual_straight_line');
            $di->monthly_straight_line = $request->input('monthly_straight_line');
            $di->current_value = $request->input('current_value');
            $di->expected_replacement_date = $request->input('expected_replacement_date');
            $di->save();

        }
        //$facility = auth()->user()->facility;
        //activity()->by($facility)->performedOn($di)->withProperties(['key'=>'edited', 'user_id' => auth()->user()->id])->log('Financial Status updated by : '. auth()->user()->name);
        return json_encode($di);
    }

    public function save_display_modal(Request $request, $id)
    {
        $userId = $request->session()->get('id');
        $user = \App\Models\User::find($userId);
        $userRole = $request->session()->get('role');

        $display = \App\Models\Display::with('workstation.workgroup.facility')->findOrFail($id);
        $facility = optional(optional($display->workstation)->workgroup)->facility;

        if ($userRole !== 'super' && (!$user || !$facility || (int) $facility->id !== (int) $user->facility_id)) {
            abort(404);
        }

        if (!$this->display_can_manage($userRole)) {
            return response()->json(['message' => 'You are not allowed to edit this display.'], 403);
        }

        $request->validate([
            'exclude' => 'nullable|boolean',
            'CommunicationType' => 'nullable|string|max:50',
            'InternalSensor' => 'nullable|boolean',
            'CurrentLUTIndex' => 'nullable|string|max:255',
            'BacklightStabilization' => 'nullable|string|max:255',
            'InstalationDate' => 'nullable|date',
            'Manufacturer' => 'nullable|string|max:255',
            'Model' => 'nullable|string|max:255',
            'SerialNumber' => 'nullable|string|max:255',
            'InventoryNumber' => 'nullable|string|max:255',
            'TypeOfDisplay' => 'nullable|string|max:255',
            'DisplayTechnology' => 'nullable|string|max:255',
            'ScreenSize' => 'nullable|string|max:255',
            'ResolutionHorizontal' => 'nullable|string|max:255',
            'ResolutionVertical' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'initial_value' => 'nullable|numeric',
            'expected_value' => 'nullable|numeric',
            'annual_straight_line' => 'nullable|numeric',
            'monthly_straight_line' => 'nullable|numeric',
            'current_value' => 'nullable|numeric',
            'expected_replacement_date' => 'nullable|date',
        ]);

        $preferences = [
            'exclude' => $request->boolean('exclude') ? 1 : 0,
            'CommunicationType' => $request->filled('CommunicationType') ? $request->input('CommunicationType') : '3',
            'InternalSensor' => $request->boolean('InternalSensor') ? 'true' : 'false',
            'CurrentLUTIndex' => $request->input('CurrentLUTIndex', ''),
            'BacklightStabilization' => $request->input('BacklightStabilization', ''),
            'InstalationDate' => $request->input('InstalationDate', ''),
            'Manufacturer' => $request->input('Manufacturer', ''),
            'Model' => $request->input('Model', ''),
            'SerialNumber' => $request->input('SerialNumber', ''),
            'InventoryNumber' => $request->input('InventoryNumber', ''),
            'TypeOfDisplay' => $request->input('TypeOfDisplay', ''),
            'DisplayTechnology' => $request->input('DisplayTechnology', ''),
            'ScreenSize' => $request->input('ScreenSize', ''),
            'ResolutionHorizontal' => $request->input('ResolutionHorizontal', ''),
            'ResolutionVertical' => $request->input('ResolutionVertical', ''),
        ];

        DB::transaction(function () use ($display, $request, $preferences) {
            foreach ($preferences as $key => $value) {
                $preference = \App\Models\DisplayPreference::firstOrNew([
                    'display_id' => $display->id,
                    'name' => $key,
                ]);

                if (!$preference->exists || (string) $preference->value !== (string) $value) {
                    $preference->value = $value;
                    $preference->sync = $preference->exists ? 0 : 1;
                    $preference->save();
                }
            }

            $display->purchase_date = $request->input('purchase_date');
            $display->initial_value = $request->input('initial_value');
            $display->expected_value = $request->input('expected_value');
            $display->annual_straight_line = $request->input('annual_straight_line');
            $display->monthly_straight_line = $request->input('monthly_straight_line');
            $display->current_value = $request->input('current_value');
            $display->expected_replacement_date = $request->input('expected_replacement_date');
            $display->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Display settings updated successfully.',
        ]);
    }

    protected function display_setting_options(int $workstationId, ?string $lutNamesValue = null, array $selectedValues = []): array
    {
        $settingOptions = \App\Models\SettingName::where('workstation_id', $workstationId)
            ->whereIn('setting_name', ['TypeOfDisplay', 'DisplayTechnology', 'ScreenSize', 'BacklightStabilization'])
            ->pluck('setting_value', 'setting_name')
            ->toArray();

        $decodeOptions = function ($value) {
            $decoded = json_decode($value ?? '[]', true);
            if (!is_array($decoded)) {
                return [];
            }

            return collect($decoded)
                ->filter(fn ($item) => $item !== null && $item !== '')
                ->map(fn ($item) => ['value' => (string) $item, 'label' => (string) $item])
                ->unique('value')
                ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
                ->all();
        };

        $options = [
            'TypeOfDisplay' => $decodeOptions($settingOptions['TypeOfDisplay'] ?? null),
            'DisplayTechnology' => $decodeOptions($settingOptions['DisplayTechnology'] ?? null),
            'ScreenSize' => $decodeOptions($settingOptions['ScreenSize'] ?? null),
            'BacklightStabilization' => $decodeOptions($settingOptions['BacklightStabilization'] ?? null),
            'lut_names' => collect(explode('||', (string) $lutNamesValue))
            ->filter(fn ($item) => $item !== null && trim((string) $item) !== '')
            ->map(fn ($item) => ['value' => (string) $item, 'label' => (string) $item])
            ->unique('value')
            ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all(),
        ];

        foreach ($selectedValues as $key => $value) {
            $normalized = trim((string) $value);
            if ($normalized === '' || !array_key_exists($key, $options)) {
                continue;
            }

            if (!collect($options[$key])->contains(fn ($item) => (string) ($item['value'] ?? '') === $normalized)) {
                $options[$key][] = ['value' => $normalized, 'label' => $normalized];
                $options[$key] = collect($options[$key])
                    ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->all();
            }
        }

        return $options;
    }

    public function edit_display_modal(Request $request, $id)
    {
        $userId = $request->session()->get('id');
        $user = \App\Models\User::find($userId);
        $userRole = $request->session()->get('role');

        $display = \App\Models\Display::with(['preferences', 'workstation.workgroup.facility'])->findOrFail($id);
        $facility = optional(optional($display->workstation)->workgroup)->facility;

        if ($userRole !== 'super' && (!$user || !$facility || (int) $facility->id !== (int) $user->facility_id)) {
            abort(404);
        }

        $preferences = $display->preferences->pluck('value', 'name');
        $options = $this->display_setting_options((int) $display->workstation_id, (string) $preferences->get('lut_names', ''), [
            'TypeOfDisplay' => (string) $preferences->get('TypeOfDisplay', ''),
            'DisplayTechnology' => (string) $preferences->get('DisplayTechnology', ''),
            'ScreenSize' => (string) $preferences->get('ScreenSize', ''),
            'BacklightStabilization' => (string) $preferences->get('BacklightStabilization', ''),
            'lut_names' => (string) $preferences->get('CurrentLUTIndex', ''),
        ]);

        return response()->json([
            'id' => $display->id,
            'name' => trim(($preferences->get('Manufacturer') ?? $display->manufacturer ?? '') . ' ' . ($preferences->get('Model') ?? $display->model ?? '')) . ' (' . ($preferences->get('SerialNumber') ?? $display->serial ?? '') . ')',
            'fields' => [
                'exclude' => (string) ($preferences->get('exclude', '0')) === '1',
                'CommunicationType' => (string) $preferences->get('CommunicationType', '3'),
                'InternalSensor' => (string) $preferences->get('InternalSensor', 'false') === 'true',
                'CurrentLUTIndex' => (string) $preferences->get('CurrentLUTIndex', ''),
                'BacklightStabilization' => (string) $preferences->get('BacklightStabilization', ''),
                'InstalationDate' => str_replace('.', '-', (string) $preferences->get('InstalationDate', '')),
                'Manufacturer' => (string) $preferences->get('Manufacturer', $display->manufacturer ?? ''),
                'Model' => (string) $preferences->get('Model', $display->model ?? ''),
                'SerialNumber' => (string) $preferences->get('SerialNumber', $display->serial ?? ''),
                'InventoryNumber' => (string) $preferences->get('InventoryNumber', ''),
                'TypeOfDisplay' => (string) $preferences->get('TypeOfDisplay', ''),
                'DisplayTechnology' => (string) $preferences->get('DisplayTechnology', ''),
                'ScreenSize' => (string) $preferences->get('ScreenSize', ''),
                'ResolutionHorizontal' => (string) $preferences->get('ResolutionHorizontal', ''),
                'ResolutionVertical' => (string) $preferences->get('ResolutionVertical', ''),
                'purchase_date' => $display->purchase_date ? str_replace('.', '-', (string) $display->purchase_date) : '',
                'initial_value' => $display->initial_value,
                'expected_value' => $display->expected_value,
                'annual_straight_line' => $display->annual_straight_line,
                'monthly_straight_line' => $display->monthly_straight_line,
                'current_value' => $display->current_value,
                'expected_replacement_date' => $display->expected_replacement_date ? str_replace('.', '-', (string) $display->expected_replacement_date) : '',
            ],
            'options' => $options,
            'permissions' => [
                'edit' => $this->display_can_manage($userRole),
                'delete' => $this->display_can_manage($userRole),
            ],
        ]);
    }

    public function display_move_options(Request $request, $id)
    {
        return response()->json([
            'message' => 'Displays are bound to their synced workstation and cannot be moved manually.',
        ], 403);
    }

    public function display_move_workgroups(Request $request, $facilityId)
    {
        return response()->json([
            'message' => 'Displays are bound to their synced workstation and cannot be moved manually.',
        ], 403);
    }

    public function display_move_workstations(Request $request, $workgroupId)
    {
        return response()->json([
            'message' => 'Displays are bound to their synced workstation and cannot be moved manually.',
        ], 403);
    }

    public function move_display_modal(Request $request, $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Displays are bound to their synced workstation and cannot be moved manually.',
        ], 403);
    }

    public function api_display_modal(Request $request, $id)
    {
        $userId = $request->session()->get('id');
        $user = \App\Models\User::find($userId);
        $userRole = $request->session()->get('role');

        $display = \App\Models\Display::with([
            'preferences',
            'workstation.workgroup.facility',
        ])->findOrFail($id);

        $facility = optional(optional($display->workstation)->workgroup)->facility;

        if ($userRole !== 'super' && (!$user || !$facility || (int) $facility->id !== (int) $user->facility_id)) {
            abort(404);
        }

        $preferences = $display->preferences->pluck('value', 'name');
        $workstation = $display->workstation;
        $workgroup = optional($workstation)->workgroup;
        $facilityTimezone = $facility?->timezone ?: config('app.timezone', 'UTC');
        $siblingDisplays = $workstation
            ? $workstation->displays()->with('preferences')->get()->sortBy(fn($item) => $item->treetext ?: ('Display #' . $item->id))->values()
            : collect();
        $manufacturer = $preferences['Manufacturer'] ?? $display->manufacturer ?? '-';
        $model = $preferences['Model'] ?? $display->model ?? '-';
        $serial = $preferences['SerialNumber'] ?? $display->serial ?? '-';
        $displayName = trim(collect([$manufacturer, $model])->filter(fn($value) => $value && $value !== '-')->implode(' '));
        if ($serial && $serial !== '-') {
            $displayName .= ' (' . $serial . ')';
        }
        $displayName = trim($displayName) ?: $display->treetext;

        $period = $request->get('period', 'all');
        $periodMap = [
            '30d' => 30,
            '90d' => 90,
            '180d' => 180,
            '365d' => 365,
            'all' => null,
        ];
        if (!array_key_exists($period, $periodMap)) {
            $period = 'all';
        }

        $historyQuery = \App\Models\History::where('display_id', $display->id);
        if ($periodMap[$period] !== null) {
            $historyQuery->where('time', '>=', now()->subDays($periodMap[$period])->timestamp);
        }

        $periodHistories = (clone $historyQuery)
            ->orderBy('time')
            ->get();

        $recentHistories = (clone $historyQuery)
            ->orderByDesc('time')
            ->limit(24)
            ->get();

        $historyCounts = (clone $historyQuery)
            ->selectRaw('result, COUNT(*) as total')
            ->groupBy('result')
            ->pluck('total', 'result');

        $totalHistories = (int) $historyCounts->sum();
        $passedHistories = (int) ($historyCounts[2] ?? 0);
        $failedHistories = (int) ($historyCounts[3] ?? 0);
        $passRate = $totalHistories > 0 ? round(($passedHistories / $totalHistories) * 100) : 0;

        $latestError = 'No active device alert reported.';
        $liveErrors = collect();
        $errorRows = json_decode($display->errors ?? '[]', true);
        if (is_array($errorRows) && count($errorRows) > 0) {
            $liveErrors = collect($errorRows)
                ->map(function ($entry) {
                    if (is_array($entry)) {
                        return trim((string) ($entry['message'] ?? $entry['error'] ?? $entry['text'] ?? $entry['name'] ?? ''));
                    }

                    return trim((string) $entry);
                })
                ->filter(fn ($value) => $value !== '')
                ->values();

            if ($liveErrors->isNotEmpty()) {
                $latestError = $liveErrors->last();
            }
        }
        $displayFailed = (int) $display->status !== 1;
        $statusSummary = !$totalHistories
            ? ($displayFailed
                ? 'This live device alert comes from the latest synced device telemetry. No calibration history has been recorded yet.'
                : 'No calibration history has been recorded yet. Current health is based on the latest synced device telemetry.')
            : ($displayFailed
                ? 'This live device alert reflects the latest synced device telemetry. The history widgets below only summarize recorded calibration runs.'
                : 'Current health reflects the latest synced device telemetry while the overview below summarizes recorded calibration runs.');

        $hoursBaseQuery = \App\Models\DisplayHour::where('display_id', $display->id);
        $hoursCount = (int) (clone $hoursBaseQuery)->count();
        $latestHoursEntry = (clone $hoursBaseQuery)
            ->orderByDesc('start')
            ->orderByDesc('id')
            ->first();
        $firstHoursEntry = (clone $hoursBaseQuery)
            ->orderBy('start')
            ->orderBy('id')
            ->first();
        $peakHours = (clone $hoursBaseQuery)->max('duration');

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

        $latestHoursActivityAt = $latestHoursEntry?->updated_at
            ?? $latestHoursEntry?->created_at
            ?? $latestHoursEntry?->start;

        $trackingWindow = '-';
        if ($firstHoursEntry && $latestHoursEntry) {
            $trackingWindow = Carbon::parse($firstHoursEntry->start)->format('d M Y')
                . ' - '
                . Carbon::parse($latestHoursActivityAt)->format('d M Y H:i');
        }

        $runningHoursTrend = (clone $hoursBaseQuery)
            ->orderByDesc('start')
            ->orderByDesc('id')
            ->limit(12)
            ->get()
            ->sortBy('start')
            ->values()
            ->map(function ($entry) use ($formatHours) {
                return [
                    'label' => Carbon::parse($entry->start)->format('d M'),
                    'fullLabel' => Carbon::parse($entry->start)->format('d M Y'),
                    'value' => (float) ($entry->duration ?? 0),
                    'formatted' => $formatHours($entry->duration),
                ];
            });

        $chart = $recentHistories
            ->sortBy('time')
            ->values()
            ->map(function ($history) {
                $score = match ((int) $history->result) {
                    2 => 100,
                    3 => 38,
                    4 => 18,
                    5 => 8,
                    default => 0,
                };

                return [
                    'id' => $history->id,
                    'label' => \Carbon\Carbon::createFromTimestamp($history->time)->format('d M y'),
                    'score' => $score,
                    'resultLabel' => $history->result_desc ?? 'Unknown',
                    'resultTone' => match ((int) $history->result) {
                        2 => 'success',
                        3 => 'danger',
                        4, 5 => 'warning',
                        default => 'neutral',
                    },
                    'performedAt' => \Carbon\Carbon::createFromTimestamp($history->time)->format('d M Y H:i'),
                ];
            });

        $timelineConfig = match ($period) {
            '30d' => ['bucket' => 'day', 'key' => 'Y-m-d', 'label' => 'd M', 'title' => 'Daily calibration outcomes'],
            '90d' => ['bucket' => 'week', 'key' => 'o-\WW', 'label' => 'd M', 'title' => 'Weekly calibration outcomes'],
            '180d' => ['bucket' => 'week', 'key' => 'o-\WW', 'label' => 'd M', 'title' => 'Weekly calibration outcomes'],
            default => ['bucket' => 'month', 'key' => 'Y-m', 'label' => 'M Y', 'title' => 'Monthly calibration outcomes'],
        };

        $timelineGroups = [];
        foreach ($periodHistories as $history) {
            $dt = \Carbon\Carbon::createFromTimestamp($history->time);
            $bucketStart = match ($timelineConfig['bucket']) {
                'day' => $dt->copy()->startOfDay(),
                'week' => $dt->copy()->startOfWeek(),
                default => $dt->copy()->startOfMonth(),
            };
            $bucketEnd = match ($timelineConfig['bucket']) {
                'day' => $dt->copy()->endOfDay(),
                'week' => $dt->copy()->endOfWeek(),
                default => $dt->copy()->endOfMonth(),
            };
            $bucket = $bucketStart->format($timelineConfig['key']);
            if (!isset($timelineGroups[$bucket])) {
                $timelineGroups[$bucket] = [
                    'key' => $bucket,
                    'label' => $timelineConfig['bucket'] === 'week'
                        ? $bucketStart->format($timelineConfig['label'])
                        : $bucketStart->format($timelineConfig['label']),
                    'rangeLabel' => $timelineConfig['bucket'] === 'week'
                        ? $bucketStart->format('d M') . ' - ' . $bucketEnd->format('d M Y')
                        : ($timelineConfig['bucket'] === 'day'
                            ? $bucketStart->format('d M Y')
                            : $bucketStart->format('M Y')),
                    'passed' => 0,
                    'failed' => 0,
                    'other' => 0,
                    'start' => $bucketStart->timestamp,
                ];
            }

            switch ((int) $history->result) {
                case 2:
                    $timelineGroups[$bucket]['passed']++;
                    break;
                case 3:
                    $timelineGroups[$bucket]['failed']++;
                    break;
                default:
                    $timelineGroups[$bucket]['other']++;
                    break;
            }
        }

        $timeline = collect($timelineGroups)
            ->sortBy('start')
            ->values()
            ->map(function ($item) {
            $total = $item['passed'] + $item['failed'] + $item['other'];
            return [
                'key' => $item['key'],
                'label' => $item['label'],
                'rangeLabel' => $item['rangeLabel'],
                'passed' => $item['passed'],
                'failed' => $item['failed'],
                'other' => $item['other'],
                'total' => $total,
                'passedPct' => $total > 0 ? round(($item['passed'] / $total) * 100, 2) : 0,
                'failedPct' => $total > 0 ? round(($item['failed'] / $total) * 100, 2) : 0,
                'otherPct' => $total > 0 ? round(($item['other'] / $total) * 100, 2) : 0,
                'passRate' => $total > 0 ? round(($item['passed'] / $total) * 100, 2) : 0,
            ];
        });

        $resolveTimelineBucket = function ($timestamp) use ($timelineConfig) {
            $dt = \Carbon\Carbon::createFromTimestamp($timestamp);
            $bucketStart = match ($timelineConfig['bucket']) {
                'day' => $dt->copy()->startOfDay(),
                'week' => $dt->copy()->startOfWeek(),
                default => $dt->copy()->startOfMonth(),
            };
            $bucketEnd = match ($timelineConfig['bucket']) {
                'day' => $dt->copy()->endOfDay(),
                'week' => $dt->copy()->endOfWeek(),
                default => $dt->copy()->endOfMonth(),
            };

            return [
                'key' => $bucketStart->format($timelineConfig['key']),
                'rangeLabel' => $timelineConfig['bucket'] === 'week'
                    ? $bucketStart->format('d M') . ' - ' . $bucketEnd->format('d M Y')
                    : ($timelineConfig['bucket'] === 'day'
                        ? $bucketStart->format('d M Y')
                        : $bucketStart->format('M Y')),
            ];
        };

        $metricCandidates = [
            'Result Max Luminance' => ['label' => 'Max Luminance', 'unit' => 'cd/m2'],
            'Result Contrast Ratio' => ['label' => 'Contrast Ratio', 'unit' => ':1'],
            'Color Temperature' => ['label' => 'Color Temp', 'unit' => 'K'],
            'AAPM GSDF Deviation' => ['label' => 'GSDF Deviation', 'unit' => '%'],
            'DeltaE ab 2000 (average)' => ['label' => 'DeltaE Avg', 'unit' => 'dE'],
        ];
        $metricSeries = [];

        foreach ($metricCandidates as $metricName => $meta) {
            $points = [];

            foreach ($recentHistories->sortBy('time') as $history) {
                $targetStep = collect($history->steps)->first(function ($step) {
                    return ($step['name'] ?? '') === 'Target & Results' && isset($step['scores']) && is_array($step['scores']);
                });

                if (!$targetStep) {
                    continue;
                }

                $score = collect($targetStep['scores'])->first(function ($item) use ($metricName) {
                    return ($item['name'] ?? null) === $metricName;
                });

                if (!$score || !isset($score['measured'])) {
                    continue;
                }

                if (!preg_match('/-?\d+(?:\.\d+)?/', html_entity_decode(strip_tags((string) $score['measured'])), $matches)) {
                    continue;
                }

                $value = (float) $matches[0];
                $points[] = [
                    'label' => \Carbon\Carbon::createFromTimestamp($history->time)->format('d M'),
                    'value' => $value,
                    'raw' => trim(html_entity_decode(strip_tags((string) $score['measured']))),
                ];
            }

            if (count($points) >= 2) {
                $values = array_column($points, 'value');
                $metricSeries[] = [
                    'key' => $metricName,
                    'label' => $meta['label'],
                    'unit' => $meta['unit'],
                    'min' => min($values),
                    'max' => max($values),
                    'latest' => end($values),
                    'points' => array_values($points),
                ];
            }
        }

        $recentHistoryItems = $recentHistories->map(function ($history) use ($resolveTimelineBucket) {
            $bucket = $resolveTimelineBucket($history->time);
            return [
                'id' => $history->id,
                'name' => $history->name ?: 'Calibration',
                'resultLabel' => $history->result_desc ?? 'Unknown',
                'resultTone' => match ((int) $history->result) {
                    2 => 'success',
                    3 => 'danger',
                    4, 5 => 'warning',
                    default => 'neutral',
                },
                'performedAt' => \Carbon\Carbon::createFromTimestamp($history->time)->format('d M Y H:i'),
                'bucketKey' => $bucket['key'],
                'bucketRangeLabel' => $bucket['rangeLabel'],
                'url' => url('histories/' . $history->id),
            ];
        })->values();

        $latestFailedHistory = $recentHistories
            ->sortByDesc('time')
            ->first(function ($history) {
                return (int) $history->result === 3;
            });

        $extractHistoryScores = function ($history) {
            return collect($history->steps)
                ->flatMap(function ($step) {
                    return collect($step['scores'] ?? [])->map(function ($score) use ($step) {
                        $answer = $score['answer'] ?? null;

                        return [
                            'section' => $step['name'] ?? 'Step',
                            'name' => trim(strip_tags((string) ($score['name'] ?? '-'))),
                            'limit' => trim(html_entity_decode(strip_tags((string) ($score['limit'] ?? '-')))),
                            'measured' => trim(html_entity_decode(strip_tags((string) ($score['measured'] ?? '-')))),
                            'answer' => $answer,
                        ];
                    });
                })
                ->values();
        };

        $latestEvaluation = null;
        $latestScoredHistory = $periodHistories
            ->sortByDesc('time')
            ->first(function ($history) use ($extractHistoryScores) {
                return $extractHistoryScores($history)->isNotEmpty();
            });

        if (!$latestScoredHistory) {
            $latestScoredHistory = $recentHistories
                ->sortByDesc('time')
                ->first(function ($history) use ($extractHistoryScores) {
                    return $extractHistoryScores($history)->isNotEmpty();
                });
        }

        if ($latestScoredHistory) {
            $scoreRows = $extractHistoryScores($latestScoredHistory);

            $latestEvaluation = [
                'id' => $latestScoredHistory->id,
                'name' => $latestScoredHistory->name ?: 'Calibration',
                'performedAt' => \Carbon\Carbon::createFromTimestamp($latestScoredHistory->time)->format('d M Y H:i'),
                'resultLabel' => $latestScoredHistory->result_desc ?? 'Unknown',
                'resultTone' => match ((int) $latestScoredHistory->result) {
                    2 => 'success',
                    3 => 'danger',
                    4, 5 => 'warning',
                    default => 'neutral',
                },
                'totalScores' => $scoreRows->count(),
                'okScores' => $scoreRows->where('answer', 1)->count(),
                'failedScores' => $scoreRows->where('answer', 0)->count(),
                'highlights' => $scoreRows
                    ->where('answer', 0)
                    ->take(5)
                    ->map(fn($score) => [
                        'section' => $score['section'],
                        'name' => $score['name'],
                        'limit' => $score['limit'] ?: '-',
                        'measured' => $score['measured'] ?: '-',
                    ])
                    ->values(),
                'url' => url('histories/' . $latestScoredHistory->id),
            ];
        }

        $structureDisplays = $siblingDisplays->map(function ($item) use ($display) {
            $itemPreferences = $item->preferences->pluck('value', 'name');
            $itemManufacturer = $itemPreferences['Manufacturer'] ?? $item->manufacturer ?? '';
            $itemModel = $itemPreferences['Model'] ?? $item->model ?? '';
            $itemSerial = $itemPreferences['SerialNumber'] ?? $item->serial ?? '';
            $itemName = trim(collect([$itemManufacturer, $itemModel])->filter()->implode(' '));
            if ($itemSerial) {
                $itemName .= ' (' . $itemSerial . ')';
            }

            return [
                'id' => $item->id,
                'name' => trim($itemName) ?: ($item->treetext ?: ('Display #' . $item->id)),
                'active' => (int) $item->id === (int) $display->id,
                'statusTone' => (int) $item->status === 1 ? 'success' : 'danger',
                'statusLabel' => (int) $item->status === 1 ? 'Healthy' : 'Needs Attention',
                'connectedLabel' => $item->connected ? 'Online' : 'Offline',
            ];
        })->values();

        $syncCandidates = collect([
            $display->updated_at ? Carbon::parse($display->updated_at) : null,
            $workstation?->getRawOriginal('last_connected')
                ? Carbon::parse($workstation->getRawOriginal('last_connected'))
                : null,
            $latestHoursActivityAt ? Carbon::parse($latestHoursActivityAt) : null,
        ])->filter();

        $lastSyncAt = $syncCandidates->sortByDesc(fn ($date) => $date->timestamp)->first();

        return response()->json([
            'id' => $display->id,
            'name' => $displayName,
            'permissions' => [
                'edit' => $this->display_can_manage($userRole),
                'move' => false,
                'delete' => $this->display_can_manage($userRole),
            ],
            'manufacturer' => $manufacturer,
            'model' => $model,
            'serial' => $serial,
            'statusLabel' => (int) $display->status === 1 ? 'Healthy' : 'Live Device Alert',
            'statusTone' => (int) $display->status === 1 ? 'success' : 'danger',
            'statusSectionLabel' => 'Current Device Health',
            'statusSummary' => $statusSummary,
            'connectedLabel' => $display->connected ? 'Online' : 'Offline',
            'connectedTone' => $display->connected ? 'success' : 'warning',
            'resolution' => trim(($preferences['ResolutionHorizontal'] ?? '-') . ' x ' . ($preferences['ResolutionVertical'] ?? '-')),
            'inventoryNumber' => $preferences['InventoryNumber'] ?? '-',
            'typeOfDisplay' => $preferences['TypeOfDisplay'] ?? '-',
            'displayTechnology' => $preferences['DisplayTechnology'] ?? '-',
            'screenSize' => $preferences['ScreenSize'] ?? '-',
            'backlightStabilization' => $preferences['BacklightStabilization'] ?? '-',
            'installationDate' => !empty($preferences['InstalationDate']) ? str_replace('.', '-', $preferences['InstalationDate']) : '-',
            'currentLut' => $preferences['CurrentLUTIndex'] ?? '-',
            'exclude' => (string) ($preferences['exclude'] ?? '0') === '1',
            'graphicboardOnly' => (string) ($preferences['CommunicationType'] ?? '0') === '1',
            'internalSensor' => ($preferences['InternalSensor'] ?? 'false') === 'true',
            'purchaseDate' => $display->purchase_date ?: '-',
            'initialValue' => $display->initial_value ?: '-',
            'expectedValue' => $display->expected_value ?: '-',
            'annualStraightLine' => $display->annual_straight_line ?: '-',
            'monthlyStraightLine' => $display->monthly_straight_line ?: '-',
            'currentValue' => $display->current_value ?: '-',
            'expectedReplacementDate' => $display->expected_replacement_date ?: '-',
            'lastSync' => $lastSyncAt ? $lastSyncAt->copy()->timezone($facilityTimezone)->format('d M Y H:i') : '-',
            'latestError' => $latestError,
            'liveErrors' => $liveErrors->values()->all(),
            'runningHours' => [
                'available' => $hoursCount > 0,
                'latestReported' => $formatHours($latestHoursEntry?->duration),
                'latestRaw' => $latestHoursEntry?->duration,
                'peakReported' => $formatHours($peakHours),
                'peakRaw' => $peakHours,
                'recordCount' => $hoursCount,
                'lastReportedAt' => $latestHoursEntry
                    ? Carbon::parse($latestHoursEntry->start)->format('d M Y H:i')
                    : '-',
                'lastSyncUpdate' => $latestHoursActivityAt
                    ? Carbon::parse($latestHoursActivityAt)->format('d M Y H:i')
                    : '-',
                'trackingWindow' => $trackingWindow,
                'trend' => $runningHoursTrend,
            ],
            'links' => [
                'settings' => url('display-settings/' . $display->id),
                'histories' => url('histories-reports?display_id=' . $display->id),
                'calibration' => url('display-calibration?display_id=' . $display->id),
                'scheduler' => url('scheduler?display_id=' . $display->id),
            ],
            'settingsOptions' => $this->display_setting_options((int) $display->workstation_id, (string) ($preferences['lut_names'] ?? ''), [
                'TypeOfDisplay' => (string) ($preferences['TypeOfDisplay'] ?? ''),
                'DisplayTechnology' => (string) ($preferences['DisplayTechnology'] ?? ''),
                'ScreenSize' => (string) ($preferences['ScreenSize'] ?? ''),
                'BacklightStabilization' => (string) ($preferences['BacklightStabilization'] ?? ''),
                'lut_names' => (string) ($preferences['CurrentLUTIndex'] ?? ''),
            ]),
            'hierarchy' => [
                'facility' => [
                    'id' => $facility->id ?? null,
                    'name' => $facility->name ?? '-',
                ],
                'workgroup' => [
                    'id' => $workgroup->id ?? null,
                    'name' => $workgroup->name ?? '-',
                ],
                'workstation' => [
                    'id' => $workstation->id ?? null,
                    'name' => $workstation->name ?? '-',
                ],
            ],
            'structure' => [
                'facility' => [
                    'id' => $facility->id ?? null,
                    'name' => $facility->name ?? '-',
                ],
                'workgroup' => [
                    'id' => $workgroup->id ?? null,
                    'name' => $workgroup->name ?? '-',
                ],
                'workstation' => [
                    'id' => $workstation->id ?? null,
                    'name' => $workstation->name ?? '-',
                ],
                'displays' => $structureDisplays,
            ],
            'history' => [
                'total' => $totalHistories,
                'passed' => $passedHistories,
                'failed' => $failedHistories,
                'passRate' => $passRate,
                'period' => $period,
                'bucket' => $timelineConfig['bucket'],
                'timelineTitle' => $timelineConfig['title'],
                'latestEvaluation' => $latestEvaluation,
                'latestFailed' => $latestFailedHistory ? [
                    'id' => $latestFailedHistory->id,
                    'name' => $latestFailedHistory->name ?: 'Calibration',
                    'performedAt' => \Carbon\Carbon::createFromTimestamp($latestFailedHistory->time)->format('d M Y H:i'),
                    'resultLabel' => $latestFailedHistory->result_desc ?? 'Unknown',
                    'resultTone' => 'danger',
                    'url' => url('histories/' . $latestFailedHistory->id),
                ] : null,
                'chart' => $chart,
                'timeline' => $timeline,
                'metrics' => $metricSeries,
                'recent' => $recentHistoryItems,
            ],
        ]);
    }
    
    public function fetch_data_settings(Request $request)
    {
        $id=$request->input('id');
        
        $data=array();
        $data['success']=0;
        $row=\App\Models\Display_preference::where('display_id', $id)->pluck('value', 'name')->toArray();
        
        $workstation_id=\App\Models\Display::where('id', $id)->pluck('workstation_id');
        $settings=\App\Models\SettingName::where('workstation_id', $workstation_id )->pluck('setting_value', 'setting_name')->toArray();
        
        $data['content']=view('displays.settings_form', ['row'=>$row, 'settings'=>$settings])->render();
        $displays=\App\Models\Display::where('id', $id)->first();
        
        
        $data['financial']=view('displays.financial_form', ['displays'=>$displays])->render();
        
        $data['display']='<h6 class="m-0 text-right mb-1" style="color:#049FD9 !important;">'.$displays->manufacturer.' '.$displays->model.' ('.$displays->serial.')</h6>
                                            <h6 class="m-0 text-right" style="font-weight:300; color:#808080;">'.$row["ResolutionHorizontal"].' X '.$row["ResolutionVertical"].'</h6>';
        
        $data['success']=1;
        return response()->json($data);
    }
}
