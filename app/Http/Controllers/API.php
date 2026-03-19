<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Notifications\RegistrationNotification;
use DB;
use App\Events\UserActivated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\User;


class API extends Controller
{
    public function register(Request $request)
    {
    // ✅ Validate input
    $validated = $request->validate([
        'username' => 'required|string|max:255|unique:users,name',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:4',
        'fullname' => 'nullable|string|max:255',
        'facility_name' => 'nullable|string|max:255',
        'timezone' => 'nullable|string|max:150',
        'workgroup_name' => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();

    try {
        $randomPassword = Str::random(8);
        
        $check=\App\Models\User::where('email', $validated['email'])->exists();
        if($check)
        {
            return response()->json([
                'success' => false,
                'message' => 'Email already associated to another account!',
            ], 500);
        }

        $check=\App\Models\User::where('name', $validated['username'])->exists();
        if($check)
        {
            return response()->json([
                'success' => false,
                'message' => 'Username already taken! Please choose another one.',
            ], 500);
        }

        $user = User::create([
            'name' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // ✅ hash password
            'fullname' => $validated['fullname'] ?? null,
            'sync_user' => $validated['username'],
            'sync_password' => md5($randomPassword),
            'sync_password_raw' => $randomPassword,
            'activation_code' => Str::random(30) . time(),
            'status' => 0,
            'facility_name' => $validated['facility_name'] ?? null,
            'timezone' => $validated['timezone'] ?? null,
            'workgroup_name' => $validated['workgroup_name'] ?? null,
            'last_password_changed' => Carbon::now(),
        ]);

        $user->assignRole('admin');

        DB::commit();

        if (!config('app.offline')) {
            $user->notify(new RegistrationNotification($user));
        } else {
            $this->activateUser($user->activation_code);
        }

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully. Please check your email to activate your account.',
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        logger()->error($e);

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please try again.',
        ], 500);
    }
    }
    
    public function unregister(Request $request)
    {
        // ✅ Validate input
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:4',
        ]);

    try {
        
        $check=\App\Models\User::where('email',$email)->orWhere('sync_user',$email)->first();
        if(isset($check->id) AND Hash::check($validated['password'], $check->password))
        {
            $check->forceDelete();
        
            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully.',
            ], 200);
        }
        else
        {
            return response()->json([
                'success' => false,
                'message' => 'Invalid details.',
            ], 401);
        }

    } catch (\Exception $e) {
        DB::rollBack();
        logger()->error($e);

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please try again.',
        ], 500);
    }
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
        //return redirect()->to('/dashboard');
    }
}

 
