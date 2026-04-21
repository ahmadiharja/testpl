<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacilityController extends Controller
{
    public function redirect_to_facilities()
    {
        return redirect()->route('facilities.management', [], 301);
    }

    protected function normalizeWorkgroupPhone(Request $request): ?string
    {
        if (!$request->has('phone_country_code') && !$request->has('phone_local')) {
            return $request->input('phone');
        }

        $countryCode = preg_replace('/[^0-9+]/', '', (string) $request->input('phone_country_code', ''));
        $localNumber = preg_replace('/\D+/', '', (string) $request->input('phone_local', ''));

        if ($countryCode === '' && $localNumber === '') {
            return '';
        }

        if ($countryCode !== '' && !str_starts_with($countryCode, '+')) {
            $countryCode = '+' . $countryCode;
        }

        return trim($countryCode . $localNumber);
    }

    protected function resolveFacilityIdFromLookup(Request $request): void
    {
        $lookup = trim((string) $request->input('facility_lookup', ''));
        if ($lookup === '') {
            return;
        }

        if (preg_match('/#(\d+)\)?$/', $lookup, $matches)) {
            $request->merge(['facility_id' => (int) $matches[1]]);
            return;
        }

        if (ctype_digit($lookup)) {
            $request->merge(['facility_id' => (int) $lookup]);
            return;
        }

        $facilityId = \App\Models\Facility::where('name', $lookup)->value('id');
        if ($facilityId) {
            $request->merge(['facility_id' => (int) $facilityId]);
        }
    }

    protected function facility_can_manage(?string $role): bool
    {
        return in_array($role, ['super', 'admin'], true);
    }

    protected function facility_can_create(?string $role): bool
    {
        return $role === 'super';
    }

    protected function facility_can_delete(?string $role): bool
    {
        return $role === 'super';
    }

    protected function facility_modal_guard(Request $request, $id)
    {
        $userId = $request->session()->get('id');
        $user = \App\Models\User::find($userId);
        $userRole = $request->session()->get('role');

        $facility = \App\Models\Facility::with(['workgroups'])->findOrFail($id);

        if ($userRole !== 'super' && (!$user || (int) $facility->id !== (int) $user->facility_id)) {
            abort(404);
        }

        return [$facility, $user, $userRole];
    }

    public function api_facility_modal(Request $request, $id)
    {
        [$facility, $user, $userRole] = $this->facility_modal_guard($request, $id);

        $workgroups = $facility->workgroups
            ->sortBy('name')
            ->values()
            ->map(function ($workgroup) {
                return [
                    'id' => $workgroup->id,
                    'name' => $workgroup->name ?: ('Workgroup #' . $workgroup->id),
                    'address' => $workgroup->address ?: '-',
                    'phone' => $workgroup->phone ?: '-',
                ];
            });

        return response()->json([
            'id' => $facility->id,
            'name' => $facility->name ?: ('Facility #' . $facility->id),
            'description' => $facility->description ?: '',
            'location' => $facility->location ?: '',
            'timezone' => $facility->timezone ?: '',
            'permissions' => [
                'edit' => $this->facility_can_manage($userRole),
                'delete' => $this->facility_can_delete($userRole),
            ],
            'workgroups' => $workgroups,
            'summary' => [
                'workgroupCount' => $workgroups->count(),
            ],
        ]);
    }

    public function save_facility_modal(Request $request, $id)
    {
        [$facility, $user, $userRole] = $this->facility_modal_guard($request, $id);

        if (!$this->facility_can_manage($userRole)) {
            return response()->json(['message' => 'You are not allowed to edit this facility.'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9][A-Za-z0-9\s._,\'()\/-]*$/'],
            'description' => ['nullable', 'string', 'min:10', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:255'],
        ]);

        $facility->name = $validated['name'];
        $facility->description = $validated['description'] ?? '';
        $facility->location = $validated['location'] ?? '';
        $facility->timezone = $validated['timezone'] ?? '';
        $facility->save();

        return response()->json([
            'success' => true,
            'message' => 'Facility updated successfully.',
        ]);
    }

    public function facility_information(Request $request, $id=0)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role=$request->session()->get('role');

        if($id==0)
        $item = $user->facility;
        else $item=\App\Models\Facility::find($id);

        if (!$item) {
            abort(404);
        }

        if ($role !== 'super' && (int) $item->id !== (int) optional($user)->facility_id) {
            abort(404);
        }

        if($request->input('facility_update')!='')
        {
            abort_unless($this->facility_can_manage($role), 403);
            $item = \App\Models\Facility::find($item->id);
            $old = $item->name;
            $item->name = $request->input('name');
            $item->location = $request->input('location');
            $item->description = $request->input('description');
            $item->timezone = $request->input('timezone');
            $item->save();

            $request->session()->flash('success', 'Facility information updated successfully!');
            if($id==0)
                return redirect('facility-info');
            else return redirect('facility-info/'.$id);
        }

        if($request->input('id')!='')
        {
            abort_unless($this->facility_can_manage($role), 403);
            $this->resolveFacilityIdFromLookup($request);
            $phone = $this->normalizeWorkgroupPhone($request);
            $id2=$request->input('id');
            if($id2=='0')
            {
                $item = new \App\Models\Workgroup();
                $item->created_at=NOW();
                $request->session()->flash('success', 'Workgroup created successfully!');
            }
            elseif($id2!='0')
            {
                $item = \App\Models\Workgroup::find($id2);
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
                $item->phone = $phone ?? '';
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

                if($id==0)
                return redirect('facility-info');
                else return redirect('facility-info/'.$id);
        }
        
        return view('facilities.facility_information', ['title'=>'Facility Information', 'item'=>$item]);
    }
    
    public function fetch_description(Request $request){
        $data=array();
        $data['success']=0;
        $user = \App\Models\User::find($request->session()->get('id'));
        $role = $request->session()->get('role');
        
        $data['content']="<option value=''>Please select</option>";
        
        $id=$request->input('id');
        if ($role !== 'super' && (int) $id !== (int) optional($user)->facility_id) {
            return response()->json(['success' => 0, 'content' => "<option value=''>Please select</option>"], 403);
        }
        
        $row=\App\Models\Facility::where('id', $id)->get();
        foreach($row as $r)
        {
            $data['content'].="<option value='".$r->id."'>".$r->description."</option>";
        }
       
        $data['success']=1;
        return response()->json($data);
    }
    
    public function fetch_location(Request $request){
        $data=array();
        $data['success']=0;
        $user = \App\Models\User::find($request->session()->get('id'));
        $role = $request->session()->get('role');
        
        $data['content']="<option value=''>Please select</option>";
        
        $id=$request->input('id');
        if ($role !== 'super' && (int) $id !== (int) optional($user)->facility_id) {
            return response()->json(['success' => 0, 'content' => "<option value=''>Please select</option>"], 403);
        }
        
        $row=\App\Models\Facility::where('id', $id)->get();
        foreach($row as $r)
        {
            $data['content'].="<option value='".$r->id."'>".$r->location."</option>";
        }
       
        $data['success']=1;
        return response()->json($data); 
    }
    
    public function fetch_timezone(Request $request){
        $data=array();
        $data['success']=0;
        $user = \App\Models\User::find($request->session()->get('id'));
        $role = $request->session()->get('role');
        $data['content']="<option value=''>Please select</option>";
        
        $id=$request->input('id');
        if ($role !== 'super' && (int) $id !== (int) optional($user)->facility_id) {
            return response()->json(['success' => 0, 'content' => "<option value=''>Please select</option>"], 403);
        }
        $row=\App\Models\Facility::where('id', $id)->get();
        foreach($row as $r)
        {
            $data['content'].="<option value='".$r->id."'>".$r->timezone."</option>";
        }
       
        $data['success']=1;
        return response()->json($data);
    }
    
    public function facilities_management(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role = $request->session()->get('role');
        
        //add/edit facility
        if($request->input('id')!='')
        {
            if (!$this->facility_can_manage($role)) {
                abort(403);
            }

            $id=$request->input('id');
            if ($id == '0' && !$this->facility_can_create($role)) {
                abort(403);
            }

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9][A-Za-z0-9\s._,\'()\/-]*$/'],
                'description' => ['nullable', 'string', 'min:10', 'max:255'],
                'location' => ['nullable', 'string', 'max:255'],
                'timezone' => ['required', 'string', 'max:255'],
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
            if($id=='0')
            {
                $item = new \App\Models\Facility();
                $item->created_at=NOW();
                $request->session()->flash('success', 'Facility created successfully!');
            }
            elseif($id!='0')
            {
                $item = \App\Models\Facility::find($id);
                if (!$item) {
                    abort(404);
                }
                if ($role !== 'super' && (int) $item->id !== (int) $user->facility_id) {
                    abort(404);
                }
                $item->updated_at=NOW();
                $request->session()->flash('success', 'Facility updated successfully!');
            }
            
            
            $item->name = $validated['name'];
            $item->location = $validated['location'] ?? '';
            $item->description = $validated['description'] ?? '';
            $item->timezone = $validated['timezone'];
            $item->user_id = $user->id;
        
            $item->save();

        /*$facility = auth()->user()->facility;
        activity()->by($facility)->performedOn($item)->withProperties(['key'=>'new', 'user_id' => auth()->user()->id])->log('New facility by : '. auth()->user()->name);*/

        
        //event(new TreeChanged($item->id));
        
        //return redirect('/facilities')->with('success', 'Facility Created');
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => 1,
                'message' => $id == '0' ? 'Facility created successfully.' : 'Facility updated successfully.',
                'id' => $item->id,
            ]);
        }

        return redirect()->route('facilities.management');
        }
        
        return view('facilities.facilities_management', ['title' =>'Facility Management']);
    }
    
    public function form(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role = $request->session()->get('role');

        if (!$this->facility_can_manage($role)) {
            abort(403);
        }

        $data=array();
        $id=$request->input('id');
        $item = \App\Models\Facility::find($id);

        if ($id && $id !== '0') {
            if (!$item) {
                abort(404);
            }
            if ($role !== 'super' && (int) $item->id !== (int) optional($user)->facility_id) {
                abort(404);
            }
        } elseif ($id === '0' && !$this->facility_can_create($role)) {
            abort(403);
        }
        
        if(!isset($item->id))
        {
            $item = \App\Models\Facility::limit(1)->get();
            $item->id=0;
            $item->name='';
            $item->description='';
            $item->location='';
            $item->timezone=0;
        }
        
        $data['success']=1;
        $data['content'] = view('facilities.facility_form')->with('item', $item)->render();
        
        return response()->json($data);
    }
    
    public function delete(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role = $request->session()->get('role');

        if (!$this->facility_can_delete($role)) {
            return response()->json(['success' => 0, 'msg' => 'You are not allowed to delete this facility.'], 403);
        }

        $data=array();
        $data['success']=0;
        $data['msg']='';
        
        $id=$request->input('id');
        $item = \App\Models\Facility::find($id);
        if (!$item) {
            return response()->json(['success' => 0, 'msg' => 'Facility not found.'], 404);
        }
        if ($role !== 'super' && (int) $item->id !== (int) optional($user)->facility_id) {
            return response()->json(['success' => 0, 'msg' => 'Facility not found.'], 404);
        }
        if (($item->workstations->count()) > 0) {
            $data['msg']='Can not delete, because there are workstations belong to this facility!';
            return response()->json($data);
        }
        // log before delete
        // $facility = auth()->user()->facility;
        // activity()->by($facility)->performedOn($item)->withProperties(['key'=>'deleted', 'user_id' => auth()->user()->id])->log('Facility deleted by : '. auth()->user()->name);
        
        $item->delete();
        
        $data['msg']='Facility deleted successfully!';
        $data['success']=1;
        //event(new TreeChanged($id));
        
        return response()->json($data);
    }
}
