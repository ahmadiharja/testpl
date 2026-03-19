<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB;

class auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $id=$request->session()->get('id');
        if($id=='' OR $id==NULL) {
            $url=url()->current();
            $request->session()->put('next', $url);
            return redirect('login');
        }
        
        $setting=\App\Models\Setting::pluck('value', 'title')->toArray();
        
        $user=\App\Models\User::find($id);
        $role=\App\Models\ModelRoles::where([['model_type', 'App\Models\User'], ['model_id', $user->id]])->orWhere([['model_type', 'App\User'], ['model_id', $user->id]])->first();
        if($role->role_id=='1') $role='super';
        elseif($role->role_id=='2') $role='admin';
        elseif($role->role_id=='3') $role='user';
        else $role='user';

        $path=$request->path();
        if($role=='user' AND ($path=='site-settings' OR $path=='alert-settings')) return redirect('/');
        
        $request->session()->put('role',$role);
        view()->share(['id'=>$id, 'user'=>$user, 'role'=>$role, 'settings'=>$setting]);
        
        return $next($request);
    }
}
