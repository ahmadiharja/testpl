<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacilityController extends Controller
{
    protected function facility_can_manage(?string $role): bool
    {
        return in_array($role, ['super', 'admin'], true);
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
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

        if($id==0)
        $item = $user->facility;
        else $item=\App\Models\Facility::find($id);

        if($request->input('facility_update')!='')
        {
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
                $item->facility_id = $request->input('facility_id');
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
        
        $data['content']="<option value=''>Please select</option>";
        
        $id=$request->input('id');
        
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
        
        $data['content']="<option value=''>Please select</option>";
        
        $id=$request->input('id');
        
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
        $data['content']="<option value=''>Please select</option>";
        
        $id=$request->input('id');
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
            if ($id == '0' && $role !== 'super') {
                abort(403);
            }
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
            
            
            $item->name = $request->input('name');
            $item->location = $request->input('location');
            $item->description = $request->input('description');
            $item->timezone = $request->input('timezone');
            $item->user_id = $user->id;
        
            $item->save();

        /*$facility = auth()->user()->facility;
        activity()->by($facility)->performedOn($item)->withProperties(['key'=>'new', 'user_id' => auth()->user()->id])->log('New facility by : '. auth()->user()->name);*/

        
        //event(new TreeChanged($item->id));
        
        //return redirect('/facilities')->with('success', 'Facility Created');
        return redirect('facilities-management');
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
        } elseif ($id === '0' && $role !== 'super') {
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
