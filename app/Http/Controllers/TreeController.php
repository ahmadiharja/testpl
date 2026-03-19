<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TreeController extends Controller
{
    public function load_tree(Request $request, $workstation_app, $leaf = 'di')
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role=$request->session()->get('role');
        
        if (strtoupper($workstation_app)=='ALL') $workstation_app = '';
        
        $cacheKey = '';
        if ($role!='super') { // load current facility only
            $facilities = array($user->facility);
            $cacheKey = $facilities[0]->id.'_'.$workstation_app.'_'.$leaf;

        } else { // load all facilities
            $facilities = \App\Models\Facility::all();
            $cacheKey = 'ALL_'.$workstation_app.'_'.$leaf;
        }

        $cacheValue = Cache::get($cacheKey);

        if ($cacheValue) {
            //return $cacheValue;
        }    
        
        $res = [];
        foreach ($facilities as $f) {
            
            $fItem = array('id' => 'fa-'.$f->id, 'parent' => '#', 'text' => $f->name, 'type' => 'facility', 'state'=>['opened' => true]);
            $res[] = $fItem;
            
            // Load workgroups
            foreach ($f->workgroups as $wg) {
                $children = $wg->workstations->count() > 0;
                $wgItem = array('id' => 'wg-'.$wg->id, 'parent' => $fItem['id'],'text' => $wg->name, 'type' => 'workgroup',  'state'=>['opened' => true]);
                $res[] = $wgItem;
                // Load workstations    
                //$workstations = $wg->workstations()->where('app', 'LIKE', "{$workstation_app}%")->get();
                $workstations = $wg->workstations()->get();
                foreach ($workstations as $ws) {
                    $children = false;
                    $wsItem = array('id' => 'ws-'.$ws->id, 'parent'=> $wgItem['id'],'text' => $ws->name, 'type' => 'workstation',  'state'=>['opened' => true]); 
                    $res[] = $wsItem;

                    // Load displays
                    if ($leaf == 'di' AND 0) {
                        foreach ($ws->displays as $d) {
                            $exclude = $d->preference('exclude');
                            $res[] = array(
                                'id' => 'di-'.$d->id, 
                                'parent'=>$wsItem['id'], 
                                'text' => $d->treetext, 
                                'type' => 'display',
                                'icon' => ($exclude?'now-ui-icons tech_tv ui-1_simple-remove':''),
                                'li_attr'=>array('class'=>($exclude?'display-disconnected':'')),
                                'state'=> ($exclude)?['checkbox_disabled' => true, 'selected' => false]:[]
                            );
                        }
                    }
                }

                
            }

            
        }
        // set cache
        Cache::put($cacheKey, $res, 120);

        return $res;
    }
}
