<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Notifications\RegistrationNotification;
use DB;
use App\Events\UserActivated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AccountController extends Controller
{
    protected function loadSettings(): array
    {
        if (!Schema::hasTable('settings')) {
            return [];
        }

        return \App\Models\Setting::pluck('value', 'title')->toArray();
    }

    public function login (Request $request)
    {
        $user_id=$request->session()->get('id');
        //$request->session()->put('id', '64');
        //$request->session()->put('id', '32');
        if($user_id!=NULL AND $user_id!='') return redirect('dashboard');
        
        if($request->input('email')!='')
        {
            $response=array();
            $response['success']=0;
            $response['next']='';
            $email=$request->input('email');
            $pass=$request->input('password');
            $remember=$request->input('remember');
            
            $check=\App\Models\User::where('email',$email)
                ->orWhere('name', $email)
                ->orWhere('sync_user',$email)->first();
            if(isset($check->id) AND Hash::check($pass, $check->password))
            {
                if (!$check->status)
                {
                    //$response['msg']='Your account is not activated yet. Please check your email for activation link or <a href=\"/auth/resend\" >resend</a> it.';
                    $response['msg']='Your account is not activated yet. Please check your email for activation link.';
                    return response()->json($response);
                }
                if (!$check->enabled) {
                    // Get super admin user
                    $admin = \App\Models\User::find(1);
                    $response['msg']='Your account is disabled. Please contact administrator at '.$admin->email;
                    return response()->json($response);
                }

                if($remember=='1') config(['session.lifetime' => 43200]);
                $request->session()->put('id',$check->id);
                
                // Set default platform session based on user DB column
                if ($check->platform == 'perfectchroma') {
                    $request->session()->put('platform', 'perfectchroma');
                } else {
                    // Defaults to perfectlum even if 'both' (will be overridden if they choose later)
                    $request->session()->put('platform', 'perfectlum');
                }
                
                $response['success']=1;
                $response['msg']='Successfully Loggedin, redirecting...';
                
                // If user has both, redirect to choice page instead of dashboard
                if ($check->platform == 'both') {
                    $response['next']=url('choose-platform');
                } else {
                    $response['next']=url('dashboard');
                }
            }
            else{
                $response['msg']='Invalid Email or Password.';
            }
            return response()->json($response);
        }

        $setting=$this->loadSettings();
        return view('account.login', ['title'=>'Login', 'settings'=>$setting]);
    }

    public function signup(Request $request)
    {
        $setting=$this->loadSettings();
        return view('account.signup', ['title'=>'Signup', 'settings'=>$setting]);
    }

    public function choose_platform(Request $request)
    {
        $user_id=$request->session()->get('id');
        if(!$user_id) return redirect('login');
        
        return view('account.choose_platform', ['title'=>'Choose Platform']);
    }

    public function select_platform(Request $request, $platform)
    {
        $user_id=$request->session()->get('id');
        if(!$user_id) return redirect('login');
        
        if (in_array($platform, ['perfectlum', 'perfectchroma'])) {
            $request->session()->put('platform', $platform);
        }
        return redirect('dashboard');
    }

    protected function create_account(Request $request)
    {
        $data=array();
        $data['success']=0;
        $data['msg']='';

        $pass1=$request->input('password');
        $pass2=$request->input('password_confirmation');
        if($pass1!=$pass2)
        {
            $data['msg']="Passwords did not match!";
            return response()->json($data);
        }

        $email=$request->input('email');
        $check=\App\Models\User::where('email', $email)->exists();
        if($check)
        {
            $data['msg']="Email already associated to another account!";
            return response()->json($data);
        }

        $username=$request->input('username');
        $check=\App\Models\User::where('name', $username)->exists();
        if($check)
        {
            $data['msg']="Username already taken! Please choose another one.";
            return response()->json($data);
        }

        DB::beginTransaction();
        try {
            $hashed_random_password = Str::random(8);
            $user = \App\Models\User::create([
                'name' => $request->input('username'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'fullname' => $request->input('fullname'),
                'sync_user' => $request->input('username'),
                'sync_password' => md5($hashed_random_password),
                'sync_password_raw' => $hashed_random_password,
                'activation_code' => STR::random(30).time(),
                'status' => 0,
                'facility_name' => $request->input('facility_name'),
                'timezone' => $request->input('timezone'),
                'workgroup_name' => $request->input('workgroup_name'),
                'last_password_changed' => \Carbon\Carbon::now()
            ]);
            $user->save();

            $user->assignRole('admin');    

            
            DB::commit();

            // Send registeration email
            if (!config('app.offline')) {
                $user->notify(new RegistrationNotification($user));
            } else {
                // if offline mode then autoa activate
                $this->activateUser($user->activation_code);
            }
            $data['success']=1;
            $data['next']=url('login');
            $request->session()->flash('success', 'We have sent you an activation link, please check your email and follow the link to activate your account.');
            //$data['msg']="Account created successfully...";
        } catch (\Exception $exception) {
            DB::rollBack();
            logger()->error($exception);
            throw $exception;
            //return null;
            $data['success']=0;
        }

        return response()->json($data);
        //return $user;
    
    
    }

    public function forgot_password(Request $request)
    {
        $setting=$this->loadSettings();
        return view('account.forgot_password', ['title'=>'Forgot Password', 'settings'=>$setting]);
    }

    public function activateUser(Request $request, string $activationCode)
    {
        DB::beginTransaction();
        try {
            $user = \App\Models\User::where('activation_code', $activationCode)->first();
            if (!$user) {
                Log::error("The code does not exist for any user in our system.");
                return "The code does not exist for any user in our system.";
            }
            $sync_password = $user->sync_password_raw;
            $user->status          = 1;
            $user->activation_code = null;
            

            // Create facility and assign to user
            $facility = new \App\Models\Facility();
            $facility->name = $user->facility_name;
            $facility->timezone = $user->timezone;
            $facility->user_id = $user->id;
            $facility->save();

            $user->facility_id = $facility->id;
            $user->save();

            // Create new workgroup 
            $workgroup = new \App\Models\Workgroup();
            $workgroup->name = $user->workgroup_name;
            $workgroup->facility_id = $facility->id;
            $workgroup->user_id = $user->id;
            $workgroup->save();

            
            // Log activity
            //activity()->by($facility)->log('New user registered: '.$user->name);

            event(new UserActivated($user));
            DB::commit();

            $request->session()->put('id', $user->id);
            //auth()->login($user);
        } catch (\Exception $exception) {
            DB::rollBack();
            logger()->error($exception);
            return "Whoops! something went wrong.".$exception->getMessage();
        }
        return redirect()->to('/dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->put('id', '');
        $request->session()->forget('id');
        return redirect('login');
    }
}

 
