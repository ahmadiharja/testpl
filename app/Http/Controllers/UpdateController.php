<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\BuildVersionJob;

class UpdateController extends Controller
{
    public function create_build(Request $request) {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        /*$dir = base_path();
        //$ret = exec('cd '.$dir.'; php artisan build 2.3.4');
        shell_exec("cd {$dir} && nohup php artisan build 2.3.4 > storage/app/updates/log.txt 2>&1 &");
        */
        /*$exitCode = Artisan::call('build', [
            'version' => request('next_version'),
            'messages' => request('comment')
        ]);*/
        //call_in_background('build '.request('next_version').' "'.request('comment').'"');
        //exec('cd '.base_path().';nohup php artisan build '.request('next_version').' > /dev/null 2>&1 &');

        BuildVersionJob::dispatch(request('next_version'), request('comment'), $user);

        $request->session()->flash('success', 'Version '.request('next_version').' is building...You will receive an email when it is completed');
        return redirect('build-version');
    }
}
