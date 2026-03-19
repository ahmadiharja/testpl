<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TestEmailNotification;


class SettingsController extends Controller
{
    private function settingOptionPayloadHasMeaningfulValues($value)
    {
        if ($value === null || $value === '') {
            return false;
        }

        $decoded = $value;
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return trim($value) !== '';
            }
        }

        if (is_array($decoded)) {
            foreach ($decoded as $key => $item) {
                if ((string) $key !== '' || (string) $item !== '') {
                    return true;
                }
            }

            return false;
        }

        return (string) $decoded !== '';
    }

    private function mergeSettingOptionPayloads($existing, $incoming)
    {
        if (!$this->settingOptionPayloadHasMeaningfulValues($existing)) {
            return $incoming;
        }

        if (!$this->settingOptionPayloadHasMeaningfulValues($incoming)) {
            return $existing;
        }

        if (!is_string($existing) || !is_string($incoming)) {
            return $existing;
        }

        $existingDecoded = json_decode($existing, true);
        $incomingDecoded = json_decode($incoming, true);

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !is_array($existingDecoded) ||
            !is_array($incomingDecoded)
        ) {
            return $existing;
        }

        $merged = $existingDecoded;
        foreach ($incomingDecoded as $key => $value) {
            if (!array_key_exists($key, $merged) || $merged[$key] === '' || $merged[$key] === null) {
                $merged[$key] = $value;
            }
        }

        return json_encode($merged, JSON_UNESCAPED_UNICODE);
    }

    private function buildWorkstationOptionCatalog($workstationIds)
    {
        $settingNames = [
            'units',
            'LumUnits',
            'AmbientStable',
            'CalibrationPresents',
            'CalibrationType',
            'ColorTemperatureAdjustment',
            'WhiteLevel_u_extcombo',
            'UsedRegulation',
        ];

        $rows = \App\Models\SettingsName::whereIn('setting_name', $settingNames)
            ->where(function ($query) use ($workstationIds) {
                $query->whereNull('workstation_id');

                if (!empty($workstationIds)) {
                    $query->orWhereIn('workstation_id', $workstationIds);
                }
            })
            ->get(['setting_name', 'setting_value']);

        $catalog = [];
        foreach ($rows as $row) {
            $catalog[$row->setting_name] = $this->mergeSettingOptionPayloads(
                $catalog[$row->setting_name] ?? null,
                $row->setting_value
            );
        }

        return $catalog;
    }

    public function site_settings(Request $request)
    {    
        $data=\App\Models\Setting::pluck('value', 'title')->toArray();
        
        if($request->input('site')!=''){
            $site_name=$request->input('site');
            $sender_email=$request->input('email');
            $sender_name=$request->input('sender');
            
            //logo
            if($request->file('site_logo')!='')
            {
                $file=$request->file('site_logo');
            
                //Move Uploaded File
                $destinationPath = 'site_logo';
                $extension = $file->getClientOriginalExtension();
                // Rename file
                $fileName = Str::slug(Carbon::now()->toDayDateTimeString()).rand(11111, 99999) .'.' . $extension;
            
                if($file->move($destinationPath,$fileName)) {
                    $pathToImage=$destinationPath.'/'.$fileName;
                    //ImageOptimizer::optimize($pathToImage);
                    $featured_image=$pathToImage;
                    
                    \App\Models\Setting::where('title', 'Site logo')->update([
                        'value'=>$featured_image
                    ]);
                }
            }
            
            //favicon
            if($request->file('favicon')!='')
            {
                $file=$request->file('favicon');
            
                //Move Uploaded File
                $destinationPath = 'favicon';
                $extension = $file->getClientOriginalExtension();
                // Rename file
                $fileName = Str::slug(Carbon::now()->toDayDateTimeString()).rand(11111, 99999) .'.' . $extension;
            
                if($file->move($destinationPath,$fileName)) {
                    $pathToImage=$destinationPath.'/'.$fileName;
                    //ImageOptimizer::optimize($pathToImage);
                    $featured_image=$pathToImage;
                    
                    \App\Models\Setting::where('title', 'favicon')->update([
                        'value'=>$featured_image
                    ]);
                }
            }
            
            \App\Models\Setting::where('title', 'Site name')->update([
                'value'=>$site_name
            ]);
            
            \App\Models\SettingSMTP::where('id', '1')->update([
                'sendername' => $sender_name,
                'senderemail' => $sender_email
            ]);
            return redirect('site-settings');
        }

        if($request->input('host')!='')
        {
            $host=$request->input('host');
            $port=$request->input('port');
            $username=$request->input('username');
            $password=$request->input('password');
            $sender_name=$request->input('sender_name');
            $sender_email=$request->input('sender_email');

            \App\Models\SettingSMTP::where('id', '1')->update([
                'host' => $host,
                'port' => $port,
                'username' => $username,
                'password' => $password,
                'sendername' => $sender_name,
                'senderemail' => $sender_email
            ]);
            
            return redirect('site-settings');
        }

        $smtp_details=\App\Models\SettingSMTP::first();
        return view('settings.site_setting', ['title'=>'Site Settings', 'data'=>$data, 'smtp_details'=>$smtp_details]);
    }
    
    public function subscription(Request $request)
    {
        return view('settings.subscription', ['title'=>'Subscription']);
    }
    
    public function profile_settings(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        
        if($request->input('username')!=''){
            $username=$request->input('username');
            $pass=$request->input('password');
            $pass2=$request->input('password2');
            $fname=$request->input('fname');
            $email=$request->input('email');
            
            //profile image
            if($request->file('profile_image')!='')
            {
                $file=$request->file('profile_image');
            
                //Move Uploaded File
                $destinationPath = public_path('assets/images/profile-images');
                $extension = $file->getClientOriginalExtension();
                // Rename file
                $fileName = (string) Str::uuid() .'.' . $extension;

                if (!is_dir($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
            
                if($file->move($destinationPath,$fileName)) {
                    $pathToImage='assets/images/profile-images/'.$fileName;
                    //ImageOptimizer::optimize($pathToImage);
                    $featured_image=$pathToImage;

                    $oldProfileImage = $user?->profile_image;
                    if ($oldProfileImage && str_starts_with($oldProfileImage, 'assets/images/profile-images/')) {
                        $oldProfileImagePath = public_path($oldProfileImage);
                        if (is_file($oldProfileImagePath)) {
                            @unlink($oldProfileImagePath);
                        }
                    }
                    
                    \App\Models\User::where('id', $user_id)->update([
                        'profile_image'=>$featured_image
                    ]);

                    if ($user) {
                        $user->profile_image = $featured_image;
                    }
                }
            }
            
            $check=\App\Models\User::where('id', $user_id)->limit(1)->get();
            if(count($check)==1)
            {
                \App\Models\User::where('id', $user_id)->update([
                    'name'=>$username,
                    'fullname'=>$fname,
                    'email'=>$email,
                ]);

                if($pass!='')
                {
                    if($pass==$pass2)
                    {
                        \App\Models\User::where('id', $user_id)->update([
                            'password'=>Hash::make($pass)
                        ]);  
                    }
                    else
                    {
                        $request->session()->flash('error', 'Passwords did not match!');
                        return redirect('profile-settings');
                    }
                }
                $request->session()->flash('success', 'Profile details updated successfully!');
            }
            return redirect('profile-settings');
        }
        
        if($request->input('remote_user')!='')
        {
            $remote_user=$request->input('remote_user');
            $remote_password=$request->input('remote_password');
            
            \App\Models\User::where('id', $user_id)->update([
                'sync_user' => $remote_user,
                'sync_password_raw' => $remote_password,
                'sync_password' => md5($remote_password)
            ]);
            
            $request->session()->flash('success', 'Credentials updated successfully!');
            return redirect('profile-settings');
        }
        return view('settings.profile_setting', ['title'=>'Profile Settings']);
    }

    public function build_version(Request $request)
    {
        return redirect('site-settings?tab=release');
    }
    
    public function remove_image(Request $request){
        
        $user_id=$request->session()->get('id');
        $user = \App\Models\User::find($user_id);
        
        $data=array();
        $data['success']=0;

        $profileImage = $user?->profile_image;
        if ($profileImage && str_starts_with($profileImage, 'assets/images/profile-images/')) {
            $profileImagePath = public_path($profileImage);
            if (is_file($profileImagePath)) {
                @unlink($profileImagePath);
            }
        }
        
        \App\Models\User::where('id', $user_id)->update([
            'profile_image'=> null
        ]);
        
        $data['success']=1;
        return response()->json($data);
    }
    
    //alert settings
    public function alert_settings(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role=$request->session()->get('role');
        
        //add/edit form
        if($request->input('id')!='')
        {
            $id=$request->input('id');
            if($id=='0'){
                $alert= new \App\Models\Alert();
                $alert->created_at=NOW();
                $request->session()->flash('success', 'Alert created successfully!');
            }
            elseif($id!='0')
            {
                $alert= \App\Models\Alert::find($id);
                $alert->updated_at=NOW();
                $request->session()->flash('success', 'Alert updated successfully!');
            }
           
        $alert->email = $request->input('email');
        $alert->actived = $request->input('actived') ? 1 : 0;
        $alert->facility_id = $request->input('facility_id');
        $alert->daily_report = $request->input('daily_report') ? 1 : 0;
        $alert->user_id = $user->id;

        if ($request->input('alert_status_1') == 1) {
            $alert->alert_status = 1;
        }
        if ($request->input('alert_status_2') == 2) {
            $alert->alert_status = 2;
        }
        if ($request->input('alert_status_1') && $request->input('alert_status_2')) {
            $alert->alert_status = 3;
        }

        $alert->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => 1,
                'msg' => 'Alert saved successfully!',
            ]);
        }

        return redirect('alert-settings');
        }
        
        $smtp = \App\Models\SettingSMTP::first();

        $errorlimit = \App\Models\ErrorLimit::whereNotIn('id', ['all_qa_steps_ok'])->get();
        $data = array(
            'smtp' => $smtp,
            'errorlimit' => $errorlimit
        );
        
        //smtp update
        
        //errorlimit update
        
        return view('settings.alert_settings', ['title' => 'Alert Settings', 'role' => $role])->with($data);
    }
    
    public function errorlimit_update(Request $request)
    {
        //errorlimit update
        $response=array();
        $response['success']=0;
        $response['msg']='';
        $input = $request->except('_token');
        foreach ($input as $key => $data) {
            $errorlimit = \App\Models\ErrorLimit::whereIn('id', [$key])->first();
            $errorlimit->value = $data;
            $errorlimit->save();
        }
        
        $response['success']=1;
        $response['msg']='Error Limits updated successfully!';
        return response()->json( $response); 
        
    }

    public function errorsmtp_update(Request $request)
    {
        //errorlimit update
        $response=array();
        $response['success']=0;
        $response['msg']='';

            $id=$request->input('smtp_id');
            $smtp = \App\Models\SettingSMTP::findOrFail($id);

            $smtp->sendername = $request->input('sendername');
            $smtp->host = $request->input('smtpserver');
            $smtp->port = $request->input('smtpport');
            $smtp->username = $request->input('smtpuser');
            $smtp->senderemail = $request->input('senderemail');
            $smtp->password = $request->input('smtppassword');
            $smtp->encryption = $request->input('usetls') ? 'tls' : '';

            $smtp->save();
        
         $response['success']=1;
         $response['msg']='SMTP details updated successfully!';
        return response()->json( $response); 
        
    }
    
    public function update_alert(Request $request)
    {
        $data=array();
        $data['success']=0;
        $id=$request->input('id');
        $column=$request->input('column');
        $value=$request->input('value');

        if ($column === 'active') {
            $column = 'actived';
        }

        \App\Models\Alert::where('id', $id)->update([
            $column => $value
        ]);
        $data['success']=1;

        return response()->json($data);
    }
    
    public function sendTestEmail(Request $request)
    {
        $email = request('email');
        Notification::route('mail', $email)->notify(new TestEmailNotification());
        return 'Email has been sent to '.$email.' successfully!';
    }
    
    public function form(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $isSuper = $user->hasRole('super');
         
        $id=$request->input('id');
        $alert = \App\Models\Alert::find($id);

        $facilities = \App\Models\Facility::when(!$isSuper, function ($q) use($user) {
                return $q->where('id', $user->facility_id);
            })
            ->orderBy('id')
            ->pluck('name', 'id')
            ->toArray();

       if(!isset($alert->id)){
           $alert = new \App\Models\Alert();
           $alert->id = 0;
           $alert->email = '';
           $alert->actived = 1;
           $alert->daily_report = 1;
           $alert->facility_id = 0;
       }
        $data['success']=1;
        $data['content']=view('settings.alert_form')->with('alert', $alert)->with('facilities', $facilities)->render();

        return response()->json($data);
    }
    
    public function delete_alert(Request $request)
    {
        $data=array();
        $data['success']=0;
        $data['msg']='';
        
        $alert_id=$request->input('id');
        
        $alert = \App\Models\Alert::findOrFail($alert_id);
        $alert->delete();
        $data['msg']='Alert deleted successfully!';
        $data['success']=1;

        return response()->json($data);
    }
    
    //Application Settings
    public function application_settings(Request $request, $workstation_id)
    {
        $user_id=$request->session()->get('id');
        $userRole=$request->session()->get('role');
        $user=\App\Models\User::find($user_id);
        
        $workstation_app=$request->input('workstation_app');
        $leaf = 'di';
        if (strtoupper($workstation_app)=='ALL') $workstation_app = '';
        
        //$workstation_id=\App\Models\Display::where('id', $display_id)->pluck('workstation_id')->toArray();
        //$workstation_id=$workstation_id[0];
        $workgroup_id=\App\Models\Workstation::where('id', $workstation_id)->pluck('workgroup_id')->toArray();
        $workgroup_id=$workgroup_id[0];
        $facility_id=\App\Models\Workgroup::where('id', $workgroup_id)->pluck('facility_id')->toArray();
        $facility_id=$facility_id[0];
        
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
        return view('settings.application_settings', ['title' => 'Application Settings', 'facilities'=>$facilities, 'workgroups'=>$workgroups, 'workstations'=>$workstations, 'displays'=>$displays, 'workgroup_id'=>$workgroup_id, 'workstation_id'=>$workstation_id, 'display_id'=>-1, 'facility_id'=>$facility_id])->with('load_ws_id', $workstation_id);
    }
    
    public function global_settings(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);

        if (!$user->hasRole('super') ) { // load current facility only
            $facilities = $user->facility ? collect([$user->facility]) : collect();
            //var_dump($facilities); exit();

        } else { // load all facilities
            $facilities = \App\Models\Facility::all();
        }

        $facilityIds = $facilities->pluck('id')->filter()->values();
        $treeFacilities = \App\Models\Facility::with([
            'workgroups' => function ($query) {
                $query->select('id', 'facility_id', 'name')->orderBy('name');
            },
            'workgroups.workstations' => function ($query) {
                $query->select('id', 'workgroup_id', 'name')->orderBy('name');
            },
        ])
        ->when($facilityIds->isNotEmpty(), function ($query) use ($facilityIds) {
            $query->whereIn('id', $facilityIds);
        }, function ($query) {
            $query->whereRaw('1 = 0');
        })
        ->orderBy('name')
        ->get(['id', 'name']);

        $treeData = [];
        foreach ($treeFacilities as $facility) {
            $facilityId = 'fa-'.$facility->id;
            $treeData[] = [
                'id' => $facilityId,
                'parent' => '#',
                'text' => $facility->name,
                'type' => 'facility',
                'state' => ['opened' => true],
            ];

            foreach ($facility->workgroups as $workgroup) {
                $workgroupId = 'wg-'.$workgroup->id;
                $treeData[] = [
                    'id' => $workgroupId,
                    'parent' => $facilityId,
                    'text' => $workgroup->name,
                    'type' => 'workgroup',
                    'state' => ['opened' => true],
                ];

                foreach ($workgroup->workstations as $workstation) {
                    $treeData[] = [
                        'id' => 'ws-'.$workstation->id,
                        'parent' => $workgroupId,
                        'text' => $workstation->name,
                        'type' => 'workstation',
                        'state' => ['opened' => true],
                    ];
                }
            }
        }
        
        $facility_id = $user->facility_id;

        $ids = \App\Models\Workstation::when($facility_id, function($q) use ($facility_id) {
            return $q->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                     ->where('workgroups.facility_id', '=', $facility_id);
        })
        ->pluck('workstations.id')
        ->toArray();
        $user_workstations=implode(',', $ids);
        
        return view('settings.global_settings', [
            'title' => 'Global Settings',
            'facilities'=>$facilities,
            'user_workstations'=>$user_workstations,
            'treeData' => $treeData,
            'optionCatalog' => $this->buildWorkstationOptionCatalog($ids),
        ]);
    }
    
    
    public function getCategories(Request $request)
    {   
        $id = $request->get('id');
        $type = "";
        $regulation = $request->get('regulation');

        if (starts_with($id, 'fa-')){
            
            $id = str_replace('fa-','',$id);
            $f = \App\Models\Facility::find($id);
            $w = $f->workstations;
            $setting = $w->first()->settings_names()->where('setting_name', $regulation)->first();
        }
        else if (starts_with($id, 'wg-')){
            
            $id = str_replace('wg-','',$id);
            $wg = \App\Models\Workgroup::find($id);
            $w = $wg->workstations;
            
            $setting = $w->first()->settings_names()->where('setting_name', $regulation)->first();
        }
        else {
            $id = str_replace('ws-','',$id);
            $w = \App\Models\Workstation::find($id);
            
            $setting = $w->settings_names()->where('setting_name', $regulation)->first();
        }
        $res = array();
        if ($setting) {
            $data = json_decode($setting->setting_value,true);
            foreach ($data as $k=>$v) {
                $res[] = array('key'=>$k, 'value'=>$v);
            }
        }
        
        //return view('settings.application_settings', ['title' => 'Application Settings', 'res' =>$res]);
       
    }
     
}
