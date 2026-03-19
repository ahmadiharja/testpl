<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use RachidLaasri\LaravelInstaller\Middleware\canInstall;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';
    protected $maxAttempts = 3; // Default is 5
    protected $decayMinutes = 2; // Default is 1
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        
        return view('auth.login');
    }

    public function username()
    {
        return 'name';
    }

    public function resend(){
        return view('auth.resend');
    }
    
    public function authenticated(\Illuminate\Http\Request $request, $user)
    {
       
        if (!$user->status) {
          
            auth()->logout();
            // return back()->with('notification.warning', 'You need to confirm your account. We have sent you an activation code, please check your email.');
            return back()->with('notification.warning', 'Your account is not activated yet. Please check your email for activation link or <a href=\"/auth/resend\" >resend</a> it.');
        }
        if (!$user->enabled) {
            auth()->logout();
            // Get super admin user
            $admin = User::find(1);

            return back()->with('notification.warning', 'Your account is disabled. Please contact administrator at '.$admin->email);
        }
        // store facility.id in session
        if (auth()->user()->facility) {
            session(['facility.id' => auth()->user()->facility->id]);
        } else {
            session(['facility.id' => 0]);
        }
        return redirect()->intended($this->redirectPath());
    }
 
}
