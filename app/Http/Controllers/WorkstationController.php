<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkstationController extends Controller
{
    protected function workstation_can_manage(?string $role): bool
    {
        return in_array($role, ['super', 'admin'], true);
    }

    protected function unresolved_failed_display_summaries(array $displayIds): array
    {
        $displayIds = collect($displayIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($displayIds)) {
            return [];
        }

        $summaries = [];
        $seenTests = [];
        $historyRows = \App\Models\History::query()
            ->whereIn('display_id', $displayIds)
            ->whereNull('deleted_at')
            ->select(['id', 'display_id', 'name', 'regulation', 'result', 'time'])
            ->orderBy('display_id')
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->get();

        foreach ($historyRows as $history) {
            $displayId = (int) $history->display_id;
            $testKey = trim((string) ($history->regulation ?? '')) ?: trim((string) ($history->name ?? '')) ?: ('history:' . (int) $history->id);
            $compoundKey = $displayId . '|' . mb_strtolower($testKey);

            if (isset($seenTests[$compoundKey])) {
                continue;
            }

            $seenTests[$compoundKey] = true;

            if ((int) $history->result === 2) {
                continue;
            }

            if (!isset($summaries[$displayId])) {
                $summaries[$displayId] = [
                    'latest_failed_history_name' => trim((string) ($history->name ?? '')) ?: $testKey,
                    'latest_failed_history_time' => (int) $history->time,
                    'unresolved_tests_count' => 0,
                ];
            }

            $summaries[$displayId]['unresolved_tests_count']++;

            if ((int) $history->time > (int) $summaries[$displayId]['latest_failed_history_time']) {
                $summaries[$displayId]['latest_failed_history_name'] = trim((string) ($history->name ?? '')) ?: $testKey;
                $summaries[$displayId]['latest_failed_history_time'] = (int) $history->time;
            }
        }

        return $summaries;
    }

    protected function workstation_display_payload($display, array $unresolvedSummaries = [])
    {
        $preferences = $display->preferences->pluck('value', 'name');
        $manufacturer = $preferences['Manufacturer'] ?? $display->manufacturer ?? '';
        $model = $preferences['Model'] ?? $display->model ?? '';
        $serial = $preferences['SerialNumber'] ?? $display->serial ?? '';
        $name = trim(collect([$manufacturer, $model])->filter()->implode(' '));
        if ($serial) {
            $name .= ' (' . $serial . ')';
        }

        $summary = $unresolvedSummaries[(int) $display->id] ?? null;
        $hasUnresolvedFailure = $summary !== null;
        $needsAttention = (int) $display->status !== 1 || $hasUnresolvedFailure;
        $unresolvedCount = (int) ($summary['unresolved_tests_count'] ?? 0);
        $latestFailedName = trim((string) ($summary['latest_failed_history_name'] ?? ''));
        $attentionText = $hasUnresolvedFailure && $latestFailedName
            ? __('Unresolved failed test: :test', ['test' => $latestFailedName])
            : ($needsAttention ? __('Display needs attention') : __('Healthy'));

        if ($hasUnresolvedFailure && $unresolvedCount > 1) {
            $attentionText .= ' ' . __('(+:count more)', ['count' => $unresolvedCount - 1]);
        }

        return [
            'id' => $display->id,
            'name' => trim($name) ?: ($display->treetext ?: ('Display #' . $display->id)),
            'model' => $model ?: '-',
            'statusLabel' => $needsAttention ? 'Needs Attention' : 'Healthy',
            'statusTone' => $needsAttention ? 'danger' : 'success',
            'attentionText' => $attentionText,
            'hasUnresolvedFailure' => $hasUnresolvedFailure,
            'unresolvedTestsCount' => $unresolvedCount,
            'latestFailedHistoryName' => $latestFailedName ?: null,
            'connectedLabel' => 'Online',
        ];
    }

    protected function workstation_modal_guard(Request $request, $id)
    {
        $userId = $request->session()->get('id');
        $user = \App\Models\User::find($userId);
        $userRole = $request->session()->get('role');

        $workstation = \App\Models\Workstation::with([
            'workgroup.facility',
            'displays.preferences',
        ])->findOrFail($id);

        $facility = optional(optional($workstation)->workgroup)->facility;
        if ($userRole !== 'super' && (!$user || !$facility || (int) $facility->id !== (int) $user->facility_id)) {
            abort(404);
        }

        return [$workstation, $user, $userRole, $facility];
    }

    public function api_workstation_modal(Request $request, $id)
    {
        [$workstation, $user, $userRole, $facility] = $this->workstation_modal_guard($request, $id);
        $siblingWorkstationModels = optional($workstation->workgroup)->workstations()
            ->with('displays.preferences')
            ->get()
            ->sortBy('name')
            ->values();

        $displayIds = $siblingWorkstationModels
            ->flatMap(fn ($item) => $item->displays->pluck('id'))
            ->merge($workstation->displays->pluck('id'))
            ->map(fn ($item) => (int) $item)
            ->filter()
            ->unique()
            ->values()
            ->all();
        $unresolvedSummaries = $this->unresolved_failed_display_summaries($displayIds);

        $displays = $workstation->displays
            ->sortBy(function ($display) {
                return $display->treetext ?: ('Display #' . $display->id);
            })
            ->values()
            ->map(fn ($display) => $this->workstation_display_payload($display, $unresolvedSummaries));

        $siblingWorkstations = $siblingWorkstationModels
            ->map(function ($sibling) use ($workstation, $unresolvedSummaries) {
                $siblingDisplays = $sibling->displays
                    ->sortBy(function ($display) {
                        return $display->treetext ?: ('Display #' . $display->id);
                    })
                    ->values()
                    ->map(fn ($display) => $this->workstation_display_payload($display, $unresolvedSummaries));

                return [
                    'id' => $sibling->id,
                    'name' => $sibling->name ?: ('Workstation #' . $sibling->id),
                    'active' => (int) $sibling->id === (int) $workstation->id,
                    'healthyCount' => $siblingDisplays->where('statusTone', 'success')->count(),
                    'attentionCount' => $siblingDisplays->where('statusTone', 'danger')->count(),
                    'displayCount' => $siblingDisplays->count(),
                    'displays' => $siblingDisplays->values(),
                ];
            });

        return response()->json([
            'id' => $workstation->id,
            'name' => $workstation->name ?: ('Workstation #' . $workstation->id),
            'permissions' => [
                'edit' => $this->workstation_can_manage($userRole),
                'delete' => $this->workstation_can_manage($userRole),
                'changeWorkgroup' => $this->workstation_can_manage($userRole),
            ],
            'workgroup' => [
                'id' => optional($workstation->workgroup)->id,
                'name' => optional($workstation->workgroup)->name ?: '-',
            ],
            'facility' => [
                'id' => optional($facility)->id,
                'name' => optional($facility)->name ?: '-',
            ],
            'lastConnected' => $workstation->last_connected ?: '-',
            'clientVersion' => $workstation->client_version ?: '-',
            'displays' => $displays,
            'structure' => [
                'facility' => [
                    'id' => optional($facility)->id,
                    'name' => optional($facility)->name ?: '-',
                ],
                'workgroup' => [
                    'id' => optional($workstation->workgroup)->id,
                    'name' => optional($workstation->workgroup)->name ?: '-',
                ],
                'workstation' => [
                    'id' => $workstation->id,
                    'name' => $workstation->name ?: ('Workstation #' . $workstation->id),
                ],
                'workstations' => $siblingWorkstations,
            ],
        ]);
    }

    public function workstation_move_options(Request $request, $id)
    {
        [$workstation, $user, $userRole, $facility] = $this->workstation_modal_guard($request, $id);

        $facilities = \App\Models\Facility::query()
            ->where('id', optional($facility)->id)
            ->get(['id', 'name'])
            ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])
            ->values();

        $workgroups = \App\Models\Workgroup::query()
            ->where('facility_id', optional($facility)->id)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])
            ->values();

        return response()->json([
            'facilities' => $facilities,
            'workgroups' => $workgroups,
            'current' => [
                'facilityId' => optional($facility)->id,
                'workgroupId' => optional($workstation->workgroup)->id,
            ],
        ]);
    }

    public function workstation_move_workgroups(Request $request, $facilityId)
    {
        $userId = $request->session()->get('id');
        $user = \App\Models\User::find($userId);
        $userRole = $request->session()->get('role');

        if ($userRole !== 'super' && (int) $facilityId !== (int) optional($user)->facility_id) {
            abort(404);
        }

        return response()->json(
            \App\Models\Workgroup::query()
                ->where('facility_id', $facilityId)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])
                ->values()
        );
    }

    public function move_workstation_modal(Request $request, $id)
    {
        [$workstation, $user, $userRole, $currentFacility] = $this->workstation_modal_guard($request, $id);

        if (!$this->workstation_can_manage($userRole)) {
            return response()->json(['message' => 'You are not allowed to move this workstation.'], 403);
        }

        $validated = $request->validate([
            'workgroup_id' => ['required', 'integer', 'exists:workgroups,id'],
        ]);

        $workgroup = \App\Models\Workgroup::with('facility')->findOrFail($validated['workgroup_id']);
        if ((int) $workgroup->facility_id !== (int) optional($currentFacility)->id) {
            return response()->json([
                'success' => false,
                'message' => 'Workstation can only be moved to a workgroup inside the same facility.',
            ], 422);
        }

        if ($userRole !== 'super' && (int) $workgroup->facility_id !== (int) optional($user)->facility_id) {
            abort(404);
        }

        if ((int) $workstation->workgroup_id === (int) $workgroup->id) {
            return response()->json([
                'success' => false,
                'message' => 'This workstation is already in the selected workgroup.',
            ], 422);
        }

        $workstation->workgroup_id = $workgroup->id;
        $workstation->save();

        return response()->json([
            'success' => true,
            'message' => 'Workstation moved successfully.',
            'workgroup' => [
                'id' => $workgroup->id,
                'name' => $workgroup->name,
            ],
            'facility' => [
                'id' => optional($workgroup->facility)->id,
                'name' => optional($workgroup->facility)->name,
            ],
        ]);
    }

    public function workstations(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role = $request->session()->get('role');
        
        /*$workgroups_ids=\App\Models\Workgroup::where('user_id', $user_id)->pluck('id');
        $total_workstations=\App\Models\Workstation::whereIn('workgroup_id', $workgroups_ids)->count();
        
        $workstations=array(); $i=0;
        $row=\App\Models\Workstation::whereIn('workgroup_id', $workgroups_ids)->get();
        foreach($row as $r){
            $workstations[$i]['item']=$r;
            
            $row2=\App\Models\Workgroup::where('id', $r->workgroup_id)->select('facility_id', 'name')->first();
            $workstations[$i]['workgroup']=$row2;
            
            $row2=\App\Models\Facility::where('id', $workstations[$i]['workgroup']->facility_id)->pluck('name');
            $workstations[$i]['facility']=$row2;
            
            $i++;
        }*/

        if($request->input('id')!='')
        {
            if (!$this->workstation_can_manage($role)) {
                abort(403);
            }

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'workgroup_id' => ['required', 'integer', 'exists:workgroups,id'],
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => 0,
                        'message' => $validator->errors()->first(),
                        'errors' => $validator->errors(),
                    ], 422);
                }

                return back()->withErrors($validator)->withInput();
            }

            $validated = $validator->validated();

            $id=$request->input('id');
            if($id=='0')
            {
                $item = new \App\Models\Workstation();
                $item->created_at=NOW();
                $request->session()->flash('success', 'Workstation created successfully!');
            }
            elseif($id!='0')
            {
                $item = \App\Models\Workstation::find($id);
                if (!$item) {
                    abort(404);
                }
                $currentFacilityId = optional(optional($item->workgroup)->facility)->id;
                if ($role !== 'super' && (int) $currentFacilityId !== (int) $user->facility_id) {
                    abort(404);
                }
                $item->updated_at=NOW();
                $request->session()->flash('success', 'Workstation updated successfully!');
            }

                $item->name = $validated['name'];
                //$item->city = $request->input('city');
                //$item->state = $request->input('state');
                //$item->postcode = $request->input('postcode');
                //$item->fax = $request->input('fax');
                $item->user_id = $user->id;
                $targetWorkgroup = \App\Models\Workgroup::findOrFail($validated['workgroup_id']);
                if ($role !== 'super' && (int) $targetWorkgroup->facility_id !== (int) $user->facility_id) {
                    abort(403);
                }
                $item->workgroup_id = $targetWorkgroup->id;
                $item->save();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => 1,
                    'message' => $id == '0' ? 'Workstation created successfully.' : 'Workstation updated successfully.',
                    'id' => $item->id,
                ]);
            }

            return redirect()->route('workstations.management');
        }

        $workgroup_id = request('workgroup_id')?request('workgroup_id'):'';
        $items = \App\Models\Workstation::with('workgroup.facility');

        $items->when($workgroup_id, function($q) use ($workgroup_id) {
            return $q->where('workgroup_id','=',$workgroup_id);
        });
        
        $facilities = \App\Models\Facility::query()
            ->when($role !== 'super', function ($query) use ($user) {
                return $query->where('id', $user->facility_id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        $workgroupsByFacility = \App\Models\Workgroup::query()
            ->when($role !== 'super', function ($query) use ($user) {
                return $query->where('facility_id', $user->facility_id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'facility_id'])
            ->groupBy('facility_id')
            ->map(function ($items) {
                return $items->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])->values();
            });

        return view('workstations.workstations', [
            'title' => 'Workstations',
            'filters' => [
                'canChooseFacility' => $role === 'super',
                'facilities' => $facilities->map(fn ($facility) => [
                    'id' => $facility->id,
                    'name' => $facility->name,
                ])->values(),
                'workgroupsByFacility' => $workgroupsByFacility,
                'selectedFacilityId' => $role === 'super'
                    ? (string) $request->get('facility_id', '')
                    : (string) $user->facility_id,
                'selectedWorkgroupId' => (string) $request->get('workgroup_id', ''),
            ],
        ]);
    }

    public function form(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role = $request->session()->get('role');

        if (!$this->workstation_can_manage($role)) {
            abort(403);
        }

        $data=array();
        $id=$request->input('id');
        $isSuper = $user->hasRole('super');

        $facility_id = request('facility_id')?request('facility_id'):$user->facility_id;

        if($isSuper)
        $workgroups = \App\Models\Workgroup::with('facility')->pluck('name', 'id')->toArray();
        else
        $workgroups = \App\Models\Workgroup::with('facility')->where('facility_id', $facility_id)->pluck('name', 'id')->toArray();

        $item = \App\Models\Workstation::find($id);
        if ($id && $id !== '0') {
            if (!$item) {
                abort(404);
            }
            $currentFacilityId = optional(optional($item->workgroup)->facility)->id;
            if ($role !== 'super' && (int) $currentFacilityId !== (int) $user->facility_id) {
                abort(404);
            }
        }
        
        $facilities = \App\Models\Facility::when(!$isSuper, function ($q) use ($user) {
            return $q->where('id', $user->facility_id);
        })->orderBy('id')->pluck('name', 'id')->toArray();

        if(!isset($item->id))
        {
            $item = \App\Models\Workstation::limit(1)->get();
            $item->id=0;
            $item->name='';
            $item->address='';
            $item->phone='';
            $item->workgroup_id=0;
            $item->facility_id=0;
        }

        $data['success']=1;
        $data['content']=view('workstations.form')->with('item', $item)->with('facilities', $facilities)->with('workgroups', $workgroups)->render();

        return response()->json($data);
    }

    public function delete_workstation(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role = $request->session()->get('role');

        if (!$this->workstation_can_manage($role)) {
            return response()->json(['success' => 0, 'msg' => 'You are not allowed to delete this workstation.'], 403);
        }

        $data=array();
        $data['success']=0;
        $data['msg']='';
        $id=$request->input('id');

        $item = \App\Models\Workstation::findOrFail($id);
        $currentFacilityId = optional(optional($item->workgroup)->facility)->id;
        if ($role !== 'super' && (int) $currentFacilityId !== (int) optional($user)->facility_id) {
            return response()->json(['success' => 0, 'msg' => 'Workstation not found.'], 404);
        }
        $fid = $item->facility_id;
        if (($item->displays->count()) > 0) {
            $data['msg']='Record cannot be deleted, because there are displays belong to this workstation!';
            return response()->json($data);
        }
        // $facility = auth()->user()->facility;
        // activity()->by($facility)->performedOn($item)->withProperties(['key'=>'deleted', 'user_id' => auth()->user()->id])->log('Workgroup deleted by : '. auth()->user()->name);
        $item->delete();
        $data['msg']='Workstation deleted successfully!';
        $data['success']=1;

        return response()->json($data);
    }
    
    public function workstations_info(Request $request, $id)
    {
        $userId = $request->session()->get('id');
        $user = \App\Models\User::find($userId);
        $role = $request->session()->get('role');
        $item = \App\Models\Workstation::find($id);
        abort_if(!$item, 404);
        $currentFacilityId = optional(optional($item->workgroup)->facility)->id;
        if ($role !== 'super' && (int) $currentFacilityId !== (int) optional($user)->facility_id) {
            abort(404);
        }
      
        //return view('workstations.show')->with('item', $item);
        
        return view('workstations.information', ['title'=> 'Workstation Information', 'item' => $item]);
    }
}
