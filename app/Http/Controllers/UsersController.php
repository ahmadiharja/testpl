<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Display;
use App\Models\Role;
use App\Models\User;
use App\Models\Workgroup;
use App\Models\Workstation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    protected function displayPreviewLabel(Display $display): string
    {
        $label = trim((string) ($display->treetext ?? ''));
        if ($label !== '') {
            return $label;
        }

        return 'Display #' . $display->id;
    }

    protected function roleDisplayLabel(?string $role): string
    {
        return match ((string) $role) {
            'super' => 'Superadmin',
            'admin' => 'Admin',
            'user' => 'Operator',
            '', '-' => '-',
            default => Str::title(str_replace(['_', '-'], ' ', (string) $role)),
        };
    }

    protected function userAccessFootprint(?User $item, string $role): array
    {
        if (!$item) {
            return [
                'scopeType' => 'facility',
                'scopeLabel' => 'No assigned scope',
                'counts' => [
                    'facilities' => 0,
                    'workgroups' => 0,
                    'workstations' => 0,
                    'displays' => 0,
                ],
                'previews' => [
                    'facilities' => [],
                    'workgroups' => [],
                    'workstations' => [],
                    'displays' => [],
                ],
            ];
        }

        if ($role === 'super') {
            return [
                'scopeType' => 'global',
                'scopeLabel' => 'Global access across all facilities',
                'counts' => [
                    'facilities' => Facility::count(),
                    'workgroups' => Workgroup::count(),
                    'workstations' => Workstation::count(),
                    'displays' => Display::count(),
                ],
                'previews' => [
                    'facilities' => Facility::query()->orderBy('name')->limit(5)->pluck('name')->values()->all(),
                    'workgroups' => Workgroup::query()->orderBy('name')->limit(5)->pluck('name')->values()->all(),
                    'workstations' => Workstation::query()->orderBy('name')->limit(5)->pluck('name')->values()->all(),
                    'displays' => Display::query()->orderBy('manufacturer')->orderBy('model')->limit(5)->get()->map(fn ($display) => $this->displayPreviewLabel($display))->values()->all(),
                ],
            ];
        }

        $facility = $item->facility;

        if (!$facility) {
            return [
                'scopeType' => 'facility',
                'scopeLabel' => 'No assigned facility',
                'counts' => [
                    'facilities' => 0,
                    'workgroups' => 0,
                    'workstations' => 0,
                    'displays' => 0,
                ],
                'previews' => [
                    'facilities' => [],
                    'workgroups' => [],
                    'workstations' => [],
                    'displays' => [],
                ],
            ];
        }

        return [
            'scopeType' => 'facility',
            'scopeLabel' => 'Facility-bound access',
            'counts' => [
                'facilities' => 1,
                'workgroups' => $facility->workgroups()->count(),
                'workstations' => $facility->workstations()->count(),
                'displays' => $facility->displays()->count(),
            ],
            'previews' => [
                'facilities' => [$facility->name],
                'workgroups' => $facility->workgroups()->orderBy('workgroups.name')->limit(5)->pluck('workgroups.name')->values()->all(),
                'workstations' => $facility->workstations()->orderBy('workstations.name')->limit(5)->pluck('workstations.name')->values()->all(),
                'displays' => $facility->displays()->orderBy('manufacturer')->orderBy('model')->limit(5)->get()->map(fn ($display) => $this->displayPreviewLabel($display))->values()->all(),
            ],
        ];
    }

    protected function userViewSummary(?User $item, string $role): array
    {
        $facility = $item?->facility;
        $timezone = $item?->timezone ?: ($facility?->timezone ?: 'UTC');
        $lastPasswordChanged = null;

        if (!empty($item?->last_password_changed)) {
            try {
                $lastPasswordChanged = Carbon::parse($item->last_password_changed)->format('d M Y H:i');
            } catch (\Throwable $e) {
                $lastPasswordChanged = (string) $item->last_password_changed;
            }
        }

        return [
            'timezone' => $timezone,
            'lastPasswordChanged' => $lastPasswordChanged,
            'defaultWorkgroup' => trim((string) ($item?->workgroup_name ?? '')) ?: null,
            'footprint' => $this->userAccessFootprint($item, $role),
        ];
    }

    protected function canManageUsers(?User $user): bool
    {
        return $user && $user->hasAnyRole(['super', 'admin']);
    }

    public function users_management(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=User::find($user_id);

        abort_unless($this->canManageUsers($user), 403);
        
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

        if (!$this->canManageUsers($user)) {
            return response()->json(['data' => [], 'total' => 0, 'message' => 'You do not have access to manage users.'], 403);
        }
         
        $search = $request->get('search', '');
        $limit  = (int)$request->get('limit', 10);
        $page   = (int)$request->get('page', 1);
        $offset = ($page - 1) * $limit;
        $requestedFacilityId = (string) $request->get('facility_id', '');
        $includeOperators = $request->boolean('include_operators');

        $facilityId = '';
        if ($user->hasRole('super')) {
            $facilityId = $requestedFacilityId;
        } elseif ($user->facility_id) {
            $facilityId = (string) $user->facility_id;
        }

        $items = User::query()->whereNotIn('name', ['admin']);
        $items->when($user->hasRole('super') && !$includeOperators, function ($query) {
            return $query->whereDoesntHave('roles', function ($roleQuery) {
                $roleQuery->where('name', 'user');
            });
        });
        $items->when(!$user->hasRole('super'), function ($query) {
            return $query->whereDoesntHave('roles', function ($roleQuery) {
                $roleQuery->where('name', 'super');
            });
        });
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
            $role = $row->getRoleNames()->first() ?: '-';

            return [
                'id' => $row->id,
                'username' => $row->name,
                'fullname' => $row->fullname,
                'email' => $row->email,
                'facilityId' => $row->facility_id,
                'facility' => $row->facility_name ?: '-',
                'enabled' => (bool) $row->enabled,
                'role' => $role,
                'roleLabel' => $this->roleDisplayLabel($role),
            ];
        });

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function user_modal_json(Request $request, $id = null)
    {
        $currentUser = User::find($request->session()->get('id'));

        abort_unless($this->canManageUsers($currentUser), 403);

        $item = $id ? User::with('facility')->findOrFail($id) : new User();
        $isSuper = $currentUser->hasRole('super');

        if ($id && !$isSuper && ((string) $item->facility_id !== (string) $currentUser->facility_id || $item->hasRole('super'))) {
            abort(403);
        }

        $roles = Role::query()
            ->when(!$isSuper, function ($query) {
                return $query->where('name', '<>', 'super');
            })
            ->orderBy('name')
            ->pluck('name')
            ->values()
            ->map(fn ($name) => ['id' => $name, 'name' => $this->roleDisplayLabel($name)])
            ->all();

        $facilities = $this->facilityOptions($currentUser);
        $role = $id ? ($item->getRoleNames()->first() ?: '') : '';

        return response()->json([
            'id' => $item->id ?: 0,
            'username' => $item->name ?? '',
            'fullname' => $item->fullname ?? '',
            'email' => $item->email ?? '',
            'user_level' => $role,
            'user_level_label' => $this->roleDisplayLabel($role),
            'facility_id' => (string) ($item->facility_id ?? ($isSuper ? '' : $currentUser->facility_id)),
            'enabled' => (bool) ($item->enabled ?? false),
            'is_existing' => (bool) $item->id,
            'can_choose_facility' => $isSuper,
            'options' => [
                'roles' => $roles,
                'facilities' => $facilities,
            ],
            'view' => $item->id ? $this->userViewSummary($item, $role) : null,
        ]);
    }

    public function save_modal(Request $request)
    {
        $currentUser = User::find($request->session()->get('id'));

        if (!$this->canManageUsers($currentUser)) {
            return response()->json(['success' => 0, 'message' => 'You do not have access to manage users.'], 403);
        }

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
        $currentUser = User::find($request->session()->get('id'));

        if (!$this->canManageUsers($currentUser)) {
            return response()->json(['success' => 0, 'message' => 'You do not have access to manage users.'], 403);
        }

        $id=$request->input('id');
        $column=$request->input('column');
        $value=$request->input('value');
        $item = User::findOrFail($id);

        if (!$currentUser->hasRole('super')) {
            if ((string) $item->facility_id !== (string) $currentUser->facility_id || $item->hasRole('super')) {
                return response()->json(['success' => 0, 'message' => 'You do not have access to update this user.'], 403);
            }
        }

        if (!in_array($column, ['enabled'], true)) {
            return response()->json(['success' => 0, 'message' => 'This field cannot be updated inline.'], 422);
        }

        $item->{$column} = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $item->{$column} = $item->{$column} ? 1 : 0;
        $item->updated_at = now();
        $item->save();
        $data['success']=1;

        return response()->json($data);
    }
    
    public function user_form(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=User::find($user_id);

        if (!$this->canManageUsers($user)) {
            return response()->json(['success' => 0, 'message' => 'You do not have access to manage users.'], 403);
        }

        $isSuper = $user->hasRole('super');
        
        
        $response=array();
        $response['success']=0;
        //$id=$request->input('id');
        $id=$request->input('id');
        $user1 = User::find($id);
        //$isSuper = $user->hasRole('super');
        
        if ($id && $user1 && !$isSuper && ((string) $user1->facility_id !== (string) $user->facility_id || $user1->hasRole('super'))) {
            return response()->json(['success' => 0, 'message' => 'You do not have access to update this user.'], 403);
        }

        $facility_id = $user->facility_id;
        $facilities = Facility::when(!$isSuper, function ($q) use($facility_id) {
            return $q->where('id', $facility_id);
        })
            ->orderBy('name')->pluck('name', 'id')->toArray();
        
        $userlevels = Role::when(!$isSuper, function($q) {
            return $q->where('name', '<>', 'super');
        })
        ->orderBy('name')
        ->pluck('name')
        ->mapWithKeys(fn ($name) => [$name => $this->roleDisplayLabel($name)])
        ->toArray();

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

        if (!$this->canManageUsers($currentUser)) {
            return response()->json(['success' => 0, 'msg' => 'You do not have access to manage users.'], 403);
        }

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
        if (!$this->canManageUsers($currentUser)) {
            return ['success' => false, 'message' => 'You do not have access to manage users.', 'status' => 403];
        }

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
