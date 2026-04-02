<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB;
use App\Support\ClientSurface;
use App\Support\SessionActivity;

class auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (
            !$request->is('api/*') &&
            !$request->ajax() &&
            !$request->expectsJson()
        ) {
            ClientSurface::remember($request, ClientSurface::DESKTOP);
        }
        $id=$request->session()->get('id');
        if($id=='' OR $id==NULL) {
            $url=url()->current();
            $request->session()->put('next', $url);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Authentication required.',
                    'redirect' => ClientSurface::loginUrl($request),
                ], 401);
            }

            return redirect(ClientSurface::loginUrl($request));
        }

        if (SessionActivity::isExpired($request)) {
            $loginUrl = ClientSurface::loginUrl($request);
            SessionActivity::clearAuthenticatedState($request);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Your session expired due to inactivity.',
                    'redirect' => $loginUrl . (str_contains($loginUrl, '?') ? '&' : '?') . 'reason=inactive',
                ], 401);
            }

            $request->session()->flash('idle_logout_notice', 'Your session expired due to inactivity. Please sign in again.');
            return redirect($loginUrl);
        }
        
        $setting=\App\Models\Setting::pluck('value', 'title')->toArray();
        
        $user=\App\Models\User::find($id);
        if (!$user) {
            SessionActivity::clearAuthenticatedState($request);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Authentication required.',
                    'redirect' => ClientSurface::loginUrl($request),
                ], 401);
            }

            return redirect(ClientSurface::loginUrl($request));
        }

        $role=\App\Models\ModelRoles::where([['model_type', 'App\Models\User'], ['model_id', $user->id]])->orWhere([['model_type', 'App\User'], ['model_id', $user->id]])->first();
        if($role->role_id=='1') $role='super';
        elseif($role->role_id=='2') $role='admin';
        elseif($role->role_id=='3') $role='user';
        else $role='user';

        $path=$request->path();
        if($role!='super' AND ($path=='site-settings' OR $path=='build-version')) {
            abort(403);
        }
        if($role=='user' AND $path=='alert-settings') {
            abort(403);
        }
        
        $request->session()->put('role',$role);
        SessionActivity::touch($request);
        view()->share(['id'=>$id, 'user'=>$user, 'role'=>$role, 'settings'=>$setting]);
        
        return $next($request);
    }
}
