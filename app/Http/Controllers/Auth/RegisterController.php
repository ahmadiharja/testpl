<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;

use App\Models\Facility;
use App\Models\Workgroup;
use App\Http\Controllers\Auth\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Notifications\RegistrationNotification;
use DB;
use Carbon\Carbon;
use App\Events\UserActivated;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
            'facility_name' => 'required|unique:facilities,name',
            'timezone' => 'required',
            'workgroup_name' => 'required|unique:workgroups,name',
            'tos' => 'required'
        ], [
            'tos.required' => 'Please accept the terms and conditions before proceeding',
            'password.regex' => 'Password must be at least 6 characters and must contain the following character types: upper case letter, lower case letter, digit'
        ]);
    }

    /**
     * Show registration form
     *
     * @return mixed
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        DB::beginTransaction();
        try {
            $hashed_random_password = str_random(8);
            $user = User::create([
                'name' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'fullname' => $data['fullname'],
                'sync_user' => $data['username'],
                'sync_password' => md5($hashed_random_password),
                'sync_password_raw' => $hashed_random_password,
                'activation_code' => str_random(30).time(),
                'status' => 0,
                'facility_name' => $data['facility_name'],
                'timezone' => $data['timezone'],
                'workgroup_name' => $data['workgroup_name'],
                'last_password_changed' => \Carbon\Carbon::now()
            ]);

            $user->assignRole('admin');    

            
            DB::commit();

            // Send registeration email
            if (!config('app.offline')) {
                $user->notify(new RegistrationNotification($user));
            } else {
                // if offline mode then autoa activate
                $this->activateUser($user->activation_code);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            logger()->error($exception);
            throw $exception;
            return null;
        }
        return $user;
    }

    public function reauthenticated(\Illuminate\Http\Request $request){

        $this->validate($request,[
            'email' =>'required|string|email|max:255',
            'name' =>'required'

        ]);
        $email = $request->input('email');
        $name = $request->input('name');
        
        $user = User::where('email',$email)->where('name',$name)->first();
        
        if($user != null){
            $user->notify(new RegistrationNotification($user));
            return redirect('/login')->with('notification.info', 'We sent you an activation code. Check your email and click on the link to verify.');
        }
        // if can't find any username and email
        return redirect('/auth/resend')->with('notification.warning', 'We could not find any information. Please check again.');
    }

    /**
     * Activate the user with given activation code.
     * @param string $activationCode
     * @return string
     */
    public function activateUser(string $activationCode)
    {
         
        DB::beginTransaction();
        try {
            $user = User::where('activation_code', $activationCode)->first();
            if (!$user) {
                Log::error("The code does not exist for any user in our system.");
                return "The code does not exist for any user in our system.";
            }
            $sync_password = $user->sync_password_raw;
            $user->status          = 1;
            $user->activation_code = null;
            

            // Create facility and assign to user
            $facility = new Facility();
            $facility->name = $user->facility_name;
            $facility->timezone = $user->timezone;
            $facility->user_id = $user->id;
            $facility->save();

            $user->facility_id = $facility->id;
            $user->save();

            // Create new workgroup 
            $workgroup = new Workgroup();
            $workgroup->name = $user->workgroup_name;
            $workgroup->facility_id = $facility->id;
            $workgroup->user_id = $user->id;
            $workgroup->save();

            
            // Log activity
            //activity()->by($facility)->log('New user registered: '.$user->name);

            event(new UserActivated($user));
            DB::commit();

            auth()->login($user);
        } catch (\Exception $exception) {
            DB::rollBack();
            logger()->error($exception);
            return "Whoops! something went wrong.".$exception->getMessage();
        }
        return redirect()->to('/dashboard');
    }
    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(\Illuminate\Http\Request $request, $user)
    {
        if (!config('app.offline')) {
            $this->guard()->logout();
            return redirect('/login')->with('notification.info', 'We sent you an activation code. Check your email and click on the link to verify.');
        } else {
            return redirect()->to('/dashboard');
        }
        
    }
   
}
