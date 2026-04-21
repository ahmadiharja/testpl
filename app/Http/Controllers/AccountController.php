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
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Support\ClientSurface;
use App\Support\SessionActivity;

class AccountController extends Controller
{
    protected function preferredSurface(Request $request, ?string $fallback = null): string
    {
        return ClientSurface::remember($request, $fallback);
    }

    protected function resolveRoleId(int $userId): string
    {
        $role = \App\Models\ModelRoles::where([
            ['model_type', 'App\Models\User'],
            ['model_id', $userId],
        ])->orWhere([
            ['model_type', 'App\User'],
            ['model_id', $userId],
        ])->first();

        return match ((string) ($role->role_id ?? '3')) {
            '1' => 'super',
            '2' => 'admin',
            default => 'user',
        };
    }

    protected function loadSettings(): array
    {
        if (!Schema::hasTable('settings')) {
            return [];
        }

        return \App\Models\Setting::pluck('value', 'title')->toArray();
    }

    public function login (Request $request)
    {
        $preferredSurface = $this->preferredSurface($request);
        $user_id=$request->session()->get('id');
        //$request->session()->put('id', '64');
        //$request->session()->put('id', '32');
        if($user_id!=NULL AND $user_id!='' AND $request->input('email')=='') return redirect(ClientSurface::dashboardUrl($request));

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
                $request->session()->put('role', $this->resolveRoleId((int) $check->id));
                SessionActivity::touch($request);
                
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
                    $response['next']=ClientSurface::choosePlatformUrl($request);
                } else {
                    $response['next']=ClientSurface::dashboardUrl($request);
                }
            }
            else{
                $response['msg']='Invalid Email or Password.';
            }
            return response()->json($response);
        }

        if (
            in_array($request->method(), ['GET', 'HEAD'], true) &&
            !$request->ajax() &&
            !$request->expectsJson() &&
            $request->query('surface') === ClientSurface::DESKTOP
        ) {
            return redirect()->route('login');
        }

        if (
            $request->isMethod('get') &&
            !$request->ajax() &&
            !$request->expectsJson() &&
            $preferredSurface === ClientSurface::MOBILE
        ) {
            return redirect()->route('mobile.login', ['surface' => ClientSurface::MOBILE]);
        }

        $setting=$this->loadSettings();
        return response()
            ->view('account.login', ['title'=>'Login', 'settings'=>$setting, 'authMode' => 'login'])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function signup(Request $request)
    {
        $setting=$this->loadSettings();
        return response()
            ->view('account.login', ['title'=>'Signup', 'settings'=>$setting, 'authMode' => 'register'])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function choose_platform(Request $request)
    {
        $this->preferredSurface($request, ClientSurface::DESKTOP);
        $user_id=$request->session()->get('id');
        if(!$user_id) return redirect(ClientSurface::loginUrl($request));
        
        return view('account.choose_platform', ['title'=>'Choose Platform']);
    }

    public function select_platform(Request $request, $platform)
    {
        $this->preferredSurface($request, ClientSurface::DESKTOP);
        $user_id=$request->session()->get('id');
        if(!$user_id) return redirect(ClientSurface::loginUrl($request));
        
        if (in_array($platform, ['perfectlum', 'perfectchroma'])) {
            $request->session()->put('platform', $platform);
        }
        return redirect(ClientSurface::dashboardUrl($request));
    }

    public function create_account(Request $request)
    {
        $data=array();
        $data['success']=0;
        $data['msg']='';
        $surface = ClientSurface::current($request);

        $validator = Validator::make($request->all(), [
            'fullname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:6'],
            'password_confirmation' => ['required', 'string'],
            'facility_name' => ['required', 'string', 'max:100'],
            'workgroup_name' => ['required', 'string', 'max:100'],
            'timezone' => ['required', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            $data['msg'] = $validator->errors()->first();
            return response()->json($data, 422);
        }

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

        if (!in_array((string) $request->input('timezone'), timezone_identifiers_list(), true)) {
            $data['msg'] = "Selected timezone is invalid.";
            return response()->json($data, 422);
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
                'name' => trim((string) $request->input('username')),
                'email' => trim((string) $request->input('email')),
                'password' => $request->input('password'),
                'fullname' => trim((string) $request->input('fullname')),
                'sync_user' => trim((string) $request->input('username')),
                'sync_password' => md5($hashed_random_password),
                'sync_password_raw' => $hashed_random_password,
                'activation_code' => STR::random(30).time(),
                'status' => 0,
                'facility_name' => trim((string) $request->input('facility_name')),
                'timezone' => trim((string) $request->input('timezone')),
                'workgroup_name' => trim((string) $request->input('workgroup_name')),
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

            $request->session()->forget([
                'id',
                'role',
                'platform',
                SessionActivity::LAST_ACTIVITY_KEY,
                'idle_logout_notice',
            ]);
            ClientSurface::remember($request, $surface);

            $data['success']=1;
            $data['activation_required'] = !config('app.offline');
            $data['email'] = $user->email;
            $data['msg'] = !config('app.offline')
                ? 'Account created successfully. Please check your email, including the spam folder, and follow the activation link before signing in.'
                : 'Account created successfully. Your workspace is ready to use.';
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

    public function check_username(Request $request)
    {
        $username = trim((string) $request->input('username', ''));

        if ($username === '') {
            return response()->json([
                'success' => 0,
                'available' => false,
                'msg' => 'Username is required.',
            ], 422);
        }

        if (mb_strlen($username) > 100) {
            return response()->json([
                'success' => 0,
                'available' => false,
                'msg' => 'Username must not exceed 100 characters.',
            ], 422);
        }

        $taken = \App\Models\User::where('name', $username)->exists();

        return response()->json([
            'success' => 1,
            'available' => !$taken,
            'msg' => $taken
                ? 'Username already taken! Please choose another one.'
                : 'Username is available.',
        ]);
    }

    public function check_email(Request $request)
    {
        $email = trim((string) $request->input('email', ''));

        if ($email === '') {
            return response()->json([
                'success' => 0,
                'available' => false,
                'msg' => 'Email is required.',
            ], 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => 0,
                'available' => false,
                'msg' => 'Please enter a valid email address, e.g. example@mail.com.',
            ], 422);
        }

        if (mb_strlen($email) > 150) {
            return response()->json([
                'success' => 0,
                'available' => false,
                'msg' => 'Email must not exceed 150 characters.',
            ], 422);
        }

        $taken = \App\Models\User::where('email', $email)->exists();

        return response()->json([
            'success' => 1,
            'available' => !$taken,
            'msg' => $taken
                ? 'Email already associated to another account.'
                : 'Email is available.',
        ]);
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
            $request->session()->put('role', $this->resolveRoleId((int) $user->id));
            SessionActivity::touch($request);
            //auth()->login($user);
        } catch (\Exception $exception) {
            DB::rollBack();
            logger()->error($exception);
            return "Whoops! something went wrong.".$exception->getMessage();
        }
        return redirect(ClientSurface::dashboardUrl($request));
    }

    public function logout(Request $request)
    {
        $surface = ClientSurface::current($request);
        $reason = (string) $request->query('reason', '');
        $request->session()->put('id', '');
        $request->session()->forget([
            'id',
            'role',
            'platform',
            SessionActivity::LAST_ACTIVITY_KEY,
        ]);
        ClientSurface::remember($request, $surface);

        if ($reason === 'inactive') {
            $request->session()->flash('idle_logout_notice', 'Your session expired due to inactivity. Please sign in again.');
        }

        return redirect(ClientSurface::loginUrl($request));
    }

    public function heartbeat(Request $request)
    {
        SessionActivity::touch($request);

        return response()->json([
            'success' => 1,
            'last_activity_at' => $request->session()->get(SessionActivity::LAST_ACTIVITY_KEY),
        ]);
    }
}

 
