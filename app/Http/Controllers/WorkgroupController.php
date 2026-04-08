<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WorkgroupController extends Controller
{
    protected function workgroup_can_manage(?string $role): bool
    {
        return in_array($role, ['super', 'admin'], true);
    }

    protected function workgroup_modal_guard(Request $request, $id)
    {
        $userId = $request->session()->get('id');
        $user = \App\Models\User::find($userId);
        $userRole = $request->session()->get('role');

        $workgroup = \App\Models\Workgroup::with('facility')->findOrFail($id);

        if ($userRole !== 'super' && (!$user || (int) $workgroup->facility_id !== (int) $user->facility_id)) {
            abort(404);
        }

        return [$workgroup, $user, $userRole];
    }

    public function api_workgroup_modal(Request $request, $id)
    {
        [$workgroup, $user, $userRole] = $this->workgroup_modal_guard($request, $id);
        $includeStructure = $request->boolean('include_structure', false);
        $workstationPage = max(1, (int) $request->query('ws_page', 1));
        $workstationLimit = (int) $request->query('ws_limit', 24);
        $workstationLimit = min(120, max(12, $workstationLimit));
        $hasClientVersion = Schema::hasColumn('workstations', 'client_version');
        $userScope = $userRole === 'super'
            ? 'super'
            : ('facility-' . (int) optional($user)->facility_id);
        $cacheVersionKey = 'wg_modal_version_' . (int) $workgroup->id;
        $cacheVersion = (int) Cache::get($cacheVersionKey, 1);
        $cacheKey = implode(':', [
            'wg-modal',
            (int) $workgroup->id,
            $userScope,
            $includeStructure ? 'structure' : 'lite',
            'p' . $workstationPage,
            'l' . $workstationLimit,
            'v' . $cacheVersion,
        ]);

        return Cache::remember($cacheKey, now()->addSeconds(45), function () use (
            $workgroup,
            $userRole,
            $user,
            $includeStructure,
            $workstationPage,
            $workstationLimit,
            $hasClientVersion
        ) {

            $facilityOptions = \App\Models\Facility::query()
                ->when($userRole !== 'super', function ($query) use ($user) {
                    return $query->where('id', $user->facility_id);
                })
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])
                ->values();

            $workstationTotal = \App\Models\Workstation::query()
                ->where('workgroup_id', $workgroup->id)
                ->count();

            $displayAggregates = DB::table('displays')
                ->selectRaw('workstation_id')
                ->selectRaw('COUNT(*) as display_count')
                ->selectRaw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as healthy_count')
                ->selectRaw('SUM(CASE WHEN status != 1 THEN 1 ELSE 0 END) as attention_count')
                ->groupBy('workstation_id');

            $workstations = DB::table('workstations as ws')
                ->leftJoinSub($displayAggregates, 'da', function ($join) {
                    $join->on('da.workstation_id', '=', 'ws.id');
                })
                ->where('ws.workgroup_id', $workgroup->id)
                ->orderBy('ws.name')
                ->forPage($workstationPage, $workstationLimit)
                ->get(array_filter([
                    'ws.id',
                    'ws.name',
                    'ws.last_connected',
                    $hasClientVersion ? 'ws.client_version' : null,
                    DB::raw('COALESCE(da.display_count, 0) as display_count'),
                    DB::raw('COALESCE(da.healthy_count, 0) as healthy_count'),
                    DB::raw('COALESCE(da.attention_count, 0) as attention_count'),
                ]))
                ->map(function ($workstation) {
                    return [
                        'id' => (int) $workstation->id,
                        'name' => $workstation->name ?: ('Workstation #' . $workstation->id),
                        'lastConnected' => $workstation->last_connected ?: '-',
                        'clientVersion' => $workstation->client_version ?? '-',
                        'displayCount' => (int) ($workstation->display_count ?? 0),
                        'healthyCount' => (int) ($workstation->healthy_count ?? 0),
                        'attentionCount' => (int) ($workstation->attention_count ?? 0),
                    ];
                })
                ->values();

            $siblingWorkgroups = collect();
            if ($includeStructure) {
                $siblingWorkgroups = \App\Models\Workgroup::with([
                    'workstations.displays:id,workstation_id,status,name',
                ])
                    ->where('facility_id', $workgroup->facility_id)
                    ->orderBy('name')
                    ->get()
                    ->map(function ($sibling) use ($workgroup) {
                        $workstations = $sibling->workstations;
                        $displays = $workstations->flatMap(function ($workstation) {
                            return $workstation->displays;
                        })->values();

                    return [
                        'id' => $sibling->id,
                        'name' => $sibling->name ?: ('Workgroup #' . $sibling->id),
                        'active' => (int) $sibling->id === (int) $workgroup->id,
                        'workstationCount' => $workstations->count(),
                        'displayCount' => $displays->count(),
                        'healthyCount' => $displays->where('status', 1)->count(),
                        'attentionCount' => $displays->where('status', '!=', 1)->count(),
                        'workstations' => $workstations
                            ->sortBy('name')
                            ->values()
                            ->map(function ($workstation) {
                                $displays = $workstation->displays->values();

                                return [
                                    'id' => $workstation->id,
                                    'name' => $workstation->name ?: ('Workstation #' . $workstation->id),
                                    'active' => false,
                                    'displayCount' => $displays->count(),
                                    'healthyCount' => $displays->where('status', 1)->count(),
                                    'attentionCount' => $displays->where('status', '!=', 1)->count(),
                                    'displays' => $displays->map(function ($display) {
                                        return [
                                            'id' => $display->id,
                                            'name' => $display->name ?: ('Display #' . $display->id),
                                            'statusTone' => (int) $display->status === 1 ? 'success' : 'danger',
                                            'statusLabel' => (int) $display->status === 1 ? 'Healthy' : 'Needs Attention',
                                            'connectedLabel' => 'Online',
                                        ];
                                    })->values(),
                                ];
                            }),
                    ];
                    })
                    ->values();
            }

            $displayStats = \App\Models\Display::query()
                ->whereIn('workstation_id', function ($query) use ($workgroup) {
                    $query->select('id')
                        ->from('workstations')
                        ->where('workgroup_id', $workgroup->id);
                })
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as healthy')
                ->selectRaw('SUM(CASE WHEN status != 1 THEN 1 ELSE 0 END) as attention')
                ->first();

            $displayCount = (int) ($displayStats->total ?? 0);
            $healthyCount = (int) ($displayStats->healthy ?? 0);
            $attentionCount = (int) ($displayStats->attention ?? 0);

            return [
                'id' => $workgroup->id,
                'name' => $workgroup->name ?: ('Workgroup #' . $workgroup->id),
                'address' => $workgroup->address ?: '-',
                'phone' => $workgroup->phone ?: '-',
                'facility' => [
                    'id' => optional($workgroup->facility)->id,
                    'name' => optional($workgroup->facility)->name ?: '-',
                ],
                'permissions' => [
                    'changeFacility' => $userRole === 'super',
                    'edit' => $this->workgroup_can_manage($userRole),
                    'delete' => $this->workgroup_can_manage($userRole),
                ],
                'summary' => [
                    'workstationCount' => $workstationTotal,
                    'displayCount' => $displayCount,
                    'healthyCount' => $healthyCount,
                    'attentionCount' => $attentionCount,
                ],
                'settings' => [
                    'name' => $workgroup->name ?: '',
                    'address' => $workgroup->address ?: '',
                    'phone' => $workgroup->phone ?: '',
                    'facility_id' => optional($workgroup->facility)->id ? (string) optional($workgroup->facility)->id : '',
                    'facilities' => $facilityOptions,
                ],
                'workstations' => $workstations,
                'workstationsMeta' => [
                    'page' => $workstationPage,
                    'limit' => $workstationLimit,
                    'total' => $workstationTotal,
                    'hasMore' => ($workstationPage * $workstationLimit) < $workstationTotal,
                ],
                'structure' => [
                    'facility' => [
                        'id' => optional($workgroup->facility)->id,
                        'name' => optional($workgroup->facility)->name ?: '-',
                    ],
                    'workgroup' => [
                        'id' => $workgroup->id,
                        'name' => $workgroup->name ?: ('Workgroup #' . $workgroup->id),
                    ],
                    'workgroups' => $siblingWorkgroups,
                    'lazy' => !$includeStructure,
                ],
            ];
        });
    }

    public function save_workgroup_modal(Request $request, $id)
    {
        [$workgroup, $user, $userRole] = $this->workgroup_modal_guard($request, $id);

        if (!$this->workgroup_can_manage($userRole)) {
            return response()->json(['message' => 'You are not allowed to edit this workgroup.'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'facility_id' => ['nullable', 'integer', 'exists:facilities,id'],
        ]);

        $workgroup->name = $validated['name'];
        $workgroup->address = $validated['address'] ?? '';
        $workgroup->phone = $validated['phone'] ?? '';

        if ($userRole === 'super' && !empty($validated['facility_id'])) {
            $workgroup->facility_id = $validated['facility_id'];
        }

        $workgroup->save();

        Cache::increment('wg_modal_version_' . (int) $workgroup->id);

        return response()->json([
            'success' => true,
            'message' => 'Workgroup updated successfully.',
        ]);
    }

    public function workgroups(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role = $request->session()->get('role');

        $facility_ids=\App\Models\Facility::where('user_id', $user_id)->pluck('id');
        $total_workgroups=\App\Models\Workgroup::whereIn('facility_id', $facility_ids)->count();

        if($request->input('id')!='')
        {
            if (!$this->workgroup_can_manage($role)) {
                abort(403);
            }

            $id=$request->input('id');
            if($id=='0')
            {
                $item = new \App\Models\Workgroup();
                $item->created_at=NOW();
                $request->session()->flash('success', 'Workgroup created successfully!');
                if ($role !== 'super') {
                    $requestFacilityId = (int) $request->input('facility_id');
                    if ($requestFacilityId !== (int) $user->facility_id) {
                        abort(403);
                    }
                }
            }
            elseif($id!='0')
            {
                $item = \App\Models\Workgroup::find($id);
                if (!$item) {
                    abort(404);
                }
                if ($role !== 'super' && (int) $item->facility_id !== (int) $user->facility_id) {
                    abort(404);
                }
                $item->updated_at=NOW();
                $request->session()->flash('success', 'Workgroup updated successfully!');
            }

                $item->name = $request->input('name');
                $item->address = $request->input('address');
                //$item->city = $request->input('city');
                //$item->state = $request->input('state');
                //$item->postcode = $request->input('postcode');
                $item->phone = $request->input('phone');
                //$item->fax = $request->input('fax');
                $item->user_id = $user->id;
                $targetFacilityId = $role === 'super'
                    ? (int) $request->input('facility_id')
                    : (int) $user->facility_id;
                if ($targetFacilityId <= 0 || !\App\Models\Facility::where('id', $targetFacilityId)->exists()) {
                    abort(422);
                }
                $item->facility_id = $targetFacilityId;
                $item->save();

            return redirect('workgroups');
        }
        
        /*$workgroups=array(); $i=0;
        $row=\App\Models\Workgroup::whereIn('facility_id', $facility_ids)->limit(5)->get();
        foreach($row as $r){
            $workgroups[$i]['item']=$r;
            
            $row2=\App\Models\facility::where('user_id', $user_id)->pluck('name');
            $workgroups[$i]['facility']=$row2;
            
            $i++;
        }*/

        //$workgroups = \App\Models\Workgroup::find($id);
        //$facilities = \App\Models\Facility::orderBy('id')->limit(5)->pluck('name','id')->toArray();

        $facilities = \App\Models\Facility::query()
            ->when($role !== 'super', function ($query) use ($user) {
                return $query->where('id', $user->facility_id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('workgroups.workgroups', [
            'title'=>'Workgroups',
            'filters' => [
                'canChooseFacility' => $role === 'super',
                'facilities' => $facilities->map(fn ($facility) => [
                    'id' => $facility->id,
                    'name' => $facility->name,
                ])->values(),
                'selectedFacilityId' => $role === 'super'
                    ? (string) $request->get('facility_id', '')
                    : (string) $user->facility_id,
            ],
        ]);
    }

    public function delete_workgroup(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role = $request->session()->get('role');

        if (!$this->workgroup_can_manage($role)) {
            return response()->json(['success' => 0, 'msg' => 'You are not allowed to delete this workgroup.'], 403);
        }

        $data=array();
        $data['success']=0;
        $data['msg']='';
        $id=$request->input('id');

        $item = \App\Models\Workgroup::findOrFail($id);
        if ($role !== 'super' && (int) $item->facility_id !== (int) optional($user)->facility_id) {
            return response()->json(['success' => 0, 'msg' => 'Workgroup not found.'], 404);
        }
        $fid = $item->facility_id;
        if (($item->workstations->count()) > 0) {
            $data['msg']='Record cannot be deleted, because there are workstations belong to this workgroup!';
            return response()->json($data);
        }
        // $facility = auth()->user()->facility;
        // activity()->by($facility)->performedOn($item)->withProperties(['key'=>'deleted', 'user_id' => auth()->user()->id])->log('Workgroup deleted by : '. auth()->user()->name);
        $item->delete();
        $data['msg']='Workgroup deleted successfully!';
        $data['success']=1;

        return response()->json($data);
    }

    public function form(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role = $request->session()->get('role');

        if (!$this->workgroup_can_manage($role)) {
            abort(403);
        }

        $data=array();
        $id=$request->input('id');

        $item = \App\Models\Workgroup::find($id);
        if ($id && $id !== '0') {
            if (!$item) {
                abort(404);
            }
            if ($role !== 'super' && (int) $item->facility_id !== (int) $user->facility_id) {
                abort(404);
            }
        }
        $isSuper = $user->hasRole('super');
        $facilities = \App\Models\Facility::when(!$isSuper, function ($q) use ($user) {
            return $q->where('id', $user->facility_id);
        })->orderBy('id')->pluck('name', 'id')->toArray();

        if(!isset($item->id))
        {
            $item = \App\Models\Workgroup::limit(1)->get();
            $item->id=0;
            $item->name='';
            $item->address='';
            $item->phone='';
            $item->facility_id=0;
        }

        $data['success']=1;
        $data['content']=view('workgroups.form')->with('item', $item)->with('facilities', $facilities)->render();

        return response()->json($data);
    }
    
    public function workgroups_info(Request $request, $id)
    {
        $userId = $request->session()->get('id');
        $user = \App\Models\User::find($userId);
        $role = $request->session()->get('role');
        $item = \App\Models\Workgroup::find($id);
        abort_if(!$item, 404);
        if ($role !== 'super' && (int) $item->facility_id !== (int) optional($user)->facility_id) {
            abort(404);
        }
        $facilities = \App\Models\Facility::orderBy('id')->pluck('name','id')->toArray();
      
        return view('workgroups.information', ['title' => 'Workgroup Infromation', 'item' => $item, 'facilities' =>$facilities]);
    }
    
    
}
