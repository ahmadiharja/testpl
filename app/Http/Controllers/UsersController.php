<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function users_management(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=User::find($user_id);
        
        if($request->input('id')!='')
        {
            $result = $this->persistUser($request, $user);
            if ($result['success']) {
                $request->session()->flash('success', $result['message']);
            } else {
                $request->session()->flash('error', $result['message']);
            }
            return redirect('users-management');
        }

        $filters = [
            'canChooseFacility' => $user->hasRole('super'),
            'facilities' => $this->facilityOptions($user),
            'selectedFacilityId' => $user->hasRole('super') ? '' : (string) ($user->facility_id ?? ''),
        ];

        return view('users.users_management', ['title'=> 'User Management', 'filters' => $filters]);
    }
    
    public function users_list(Request $request)
    {
         $user_id=$request->session()->get('id');
         $user=User::find($user_id);
         
        $search = $request->get('search', '');
        $limit  = (int)$request->get('limit', 10);
        $page   = (int)$request->get('page', 1);
        $offset = ($page - 1) * $limit;
        $requestedFacilityId = (string) $request->get('facility_id', '');

        $facilityId = '';
        if ($user->hasRole('super')) {
            $facilityId = $requestedFacilityId;
        } elseif ($user->facility_id) {
            $facilityId = (string) $user->facility_id;
        }

        $items = User::query()->whereNotIn('name', ['admin']);
        $items->when($facilityId !== '', function ($q) use ($facilityId) {
            return $q->where('facility_id', '=', $facilityId);
        });

        $items->when($search, function ($q) use ($search) {
            return $q->where(function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                    ->orWhere('fullname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('facility_name', 'like', "%{$search}%");
            });
        });

        $total = $items->count();
        $rows = (clone $items)->orderBy('fullname')->offset($offset)->limit($limit)->get();

        $data = $rows->map(function ($row) {
            return [
                'id' => $row->id,
                'username' => $row->name,
                'fullname' => $row->fullname,
                'email' => $row->email,
                'facilityId' => $row->facility_id,
                'facility' => $row->facility_name ?: '-',
                'enabled' => (bool) $row->enabled,
                'role' => $row->getRoleNames()->first() ?: '-',
            ];
        });

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function user_modal_json(Request $request, $id = null)
    {
        $currentUser = User::find($request->session()->get('id'));
        $item = $id ? User::findOrFail($id) : new User();
        $isSuper = $currentUser->hasRole('super');

        if ($id && !$isSuper && (string) $item->facility_id !== (string) $currentUser->facility_id) {
            abort(403);
        }

        $roles = Role::query()
            ->when(!$isSuper, function ($query) {
                return $query->where('name', '<>', 'super');
            })
            ->orderBy('name')
            ->pluck('name')
            ->values()
            ->map(fn ($name) => ['id' => $name, 'name' => ucfirst($name)])
            ->all();

        $facilities = $this->facilityOptions($currentUser);
        $role = $id ? ($item->getRoleNames()->first() ?: '') : '';

        return response()->json([
            'id' => $item->id ?: 0,
            'username' => $item->name ?? '',
            'fullname' => $item->fullname ?? '',
            'email' => $item->email ?? '',
            'user_level' => $role,
            'facility_id' => (string) ($item->facility_id ?? ($isSuper ? '' : $currentUser->facility_id)),
            'enabled' => (bool) ($item->enabled ?? false),
            'is_existing' => (bool) $item->id,
            'can_choose_facility' => $isSuper,
            'options' => [
                'roles' => $roles,
                'facilities' => $facilities,
            ],
        ]);
    }

    public function save_modal(Request $request)
    {
        $currentUser = User::find($request->session()->get('id'));
        $result = $this->persistUser($request, $currentUser, true);

        if (!$result['success']) {
            return response()->json(['success' => 0, 'message' => $result['message']], $result['status']);
        }

        return response()->json(['success' => 1, 'message' => $result['message']]);
    }

    public function update_user(Request $request)
    {
        $data=array();
        $data['success']=0;
        $id=$request->input('id');
        $column=$request->input('column');
        $value=$request->input('value');

        \App\Models\User::where('id', $id)->update([
            $column => $value
        ]);
        $data['success']=1;

        return response()->json($data);
    }
    
    public function user_form(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=User::find($user_id);
        $isSuper = $user->hasRole('super');
        
        
        $response=array();
        $response['success']=0;
        //$id=$request->input('id');
        $id=$request->input('id');
        $user1 = User::find($id);
        //$isSuper = $user->hasRole('super');
        
        $facility_id = $user->facility_id;
        $facilities = Facility::when(!$isSuper, function ($q) use($facility_id) {
            return $q->where('id', $facility_id);
        })
            ->orderBy('name')->pluck('name', 'id')->toArray();
        
        $userlevels = Role::when(!$isSuper, function($q) {
            return $q->where('name', '<>', 'super');
        })
        ->pluck('name', 'name')->toArray();

        if(!isset($user1->id))
        {
            $user1 = User::limit(1)->get();
            $user1->id=0;
            $user1->name='';
            $user1->password='';
            $user1->password_confirmation='';
            $user1->fullname='';
            $user1->email='';
            $user1->user_level='';
            $user1->facility_id=0;
            $user1->enabled=0;
        }
        
        
        $data = array(
            'facilities' => $facilities,
            'user2' => $user1,
            'userlevels' => $userlevels
        );
        
        $response['success']=1;
        $response['content']= view('users.user_form')->with($data)->render();
        
        return response()->json($response);
    }
    
    public function delete(Request $request)
    {
        $data=array();
        $data['success']=0;
        $data['msg']='';
        
        $id=$request->input('id');
        $currentUser = User::find($request->session()->get('id'));
        $user = User::findOrFail($id);

        if ((int) $user->id === (int) $currentUser->id) {
            $data['msg'] = 'You cannot delete your own account.';
            return response()->json($data, 422);
        }

        if (!$currentUser->hasRole('super')) {
            if ((string) $user->facility_id !== (string) $currentUser->facility_id) {
                return response()->json(['success' => 0, 'msg' => 'You do not have access to delete this user.'], 403);
            }
            if ($user->hasRole('super')) {
                return response()->json(['success' => 0, 'msg' => 'You do not have access to delete a super user.'], 403);
            }
        }

        $user->forceDelete();
        
        $data['msg']='User deleted successfully!';
        $data['success']=1;

        return response()->json($data);
        
    }

    protected function persistUser(Request $request, User $currentUser, bool $json = false): array
    {
        $id = (string) $request->input('id', '0');
        $isNew = $id === '0' || $id === '';
        $isSuper = $currentUser->hasRole('super');
        $item = $isNew ? new User() : User::find($id);

        if (!$item) {
            return ['success' => false, 'message' => 'User not found.', 'status' => 404];
        }

        if (!$isNew && !$isSuper) {
            if ((string) $item->facility_id !== (string) $currentUser->facility_id || $item->hasRole('super')) {
                return ['success' => false, 'message' => 'You do not have access to update this user.', 'status' => 403];
            }
        }

        $username = trim((string) $request->input('name'));
        $email = trim((string) $request->input('email'));
        $fullname = trim((string) $request->input('fullname'));
        $password = (string) $request->input('password');
        $passwordConfirmation = (string) $request->input('password_confirmation');
        $roleName = (string) $request->input('user_level');
        $facilityId = (string) $request->input('facility_id', '');
        $enabled = $request->boolean('enabled');

        if (!$isSuper) {
            if ($roleName === 'super') {
                return ['success' => false, 'message' => 'You do not have access to assign super role.', 'status' => 403];
            }
            $facilityId = (string) $currentUser->facility_id;
        }

        if ($username === '' || $fullname === '' || $email === '' || $roleName === '') {
            return ['success' => false, 'message' => 'Please complete all required fields.', 'status' => 422];
        }

        if (!$isSuper || $roleName !== 'super') {
            if ($facilityId === '') {
                return ['success' => false, 'message' => 'Facility is required for this user level.', 'status' => 422];
            }
        }

        if ($password !== '' && $password !== $passwordConfirmation) {
            return ['success' => false, 'message' => 'Password confirmation does not match.', 'status' => 422];
        }

        $usernameExists = User::where('name', $username)
            ->when(!$isNew, fn ($query) => $query->where('id', '<>', $item->id))
            ->exists();
        if ($usernameExists) {
            return ['success' => false, 'message' => 'Username already taken.', 'status' => 422];
        }

        $emailExists = User::where('email', $email)
            ->when(!$isNew, fn ($query) => $query->where('id', '<>', $item->id))
            ->exists();
        if ($emailExists) {
            return ['success' => false, 'message' => 'Email already associated to another account.', 'status' => 422];
        }

        $facility = null;
        if ($facilityId !== '') {
            $facility = Facility::find($facilityId);
            if (!$facility) {
                return ['success' => false, 'message' => 'Facility not found.', 'status' => 422];
            }
        }

        if ($isNew) {
            $item->created_at = now();
            $item->sync_password_raw = Str::random(8);
            $item->sync_password = md5($item->sync_password_raw);
            $item->activation_code = Str::random(30) . time();
            $message = 'User created successfully!';
        } else {
            $item->updated_at = now();
            $message = 'User updated successfully!';
        }

        $item->name = $item->sync_user = $username;
        $item->fullname = $fullname;
        $item->email = $email;
        if ($password !== '') {
            $item->password = Hash::make($password);
        }
        $item->facility_id = $facility?->id;
        $item->facility_name = $facility?->name;
        $item->timezone = $facility?->timezone;
        $item->enabled = $enabled ? 1 : 0;
        $item->status = 1;
        $item->save();
        $item->syncRoles([$roleName]);

        return ['success' => true, 'message' => $message, 'status' => 200];
    }

    protected function facilityOptions(User $user): array
    {
        return Facility::query()
            ->when(!$user->hasRole('super'), function ($query) use ($user) {
                return $query->where('id', $user->facility_id);
            })
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($facility) => ['id' => (string) $facility->id, 'name' => $facility->name])
            ->values()
            ->all();
    }
}
