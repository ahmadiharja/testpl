<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Display;
use App\Models\Facility;
use App\Models\QATask;
use App\Models\Setting;
use App\Models\Task;
use App\Models\User;
use App\Models\Workgroup;
use App\Models\Workstation;
use App\Support\ClientSurface;
use App\Support\SessionActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AppController extends Controller
{
    protected function loadSettings(): array
    {
        if (!Schema::hasTable('settings')) {
            return [];
        }

        return Setting::pluck('value', 'title')->toArray();
    }

    protected function resolveUser(Request $request): ?User
    {
        $userId = $request->session()->get('id');

        return $userId ? User::find($userId) : null;
    }

    protected function redirectIfNotReady(Request $request): ?RedirectResponse
    {
        ClientSurface::remember($request, ClientSurface::MOBILE);

        if (SessionActivity::isExpired($request)) {
            SessionActivity::clearAuthenticatedState($request);
            $request->session()->flash('idle_logout_notice', 'Your session expired due to inactivity. Please sign in again.');

            return redirect()->route('mobile.login', ['surface' => ClientSurface::MOBILE]);
        }

        $user = $this->resolveUser($request);
        if (!$user) {
            return redirect()->route('mobile.login');
        }

        if ($user->platform === 'both' && !$request->session()->get('platform')) {
            return redirect()->route('mobile.choose-platform');
        }

        if (!$request->session()->get('platform')) {
            $request->session()->put('platform', $user->platform === 'perfectchroma' ? 'perfectchroma' : 'perfectlum');
        }

        SessionActivity::touch($request);

        return null;
    }

    protected function mobileView(Request $request, string $view, array $data = [])
    {
        $settings = $this->loadSettings();
        $user = $this->resolveUser($request);
        $platform = $request->session()->get('platform', 'perfectlum');

        return view($view, array_merge([
            'title' => $data['screenTitle'] ?? 'Mobile Workspace',
            'settings' => $settings,
            'siteName' => $settings['Site name'] ?? 'PerfectLum',
            'mobileUser' => $user,
            'mobilePlatform' => $platform,
            'platformLabel' => $platform === 'perfectchroma' ? 'PerfectChroma' : 'PerfectLum',
        ], $data));
    }

    protected function dashboardSummary(Request $request, ?User $user): array
    {
        if (!$user) {
            return [
                'dashboardSummary' => [
                    'displaysOk' => 0,
                    'displaysFailed' => 0,
                    'workstations' => 0,
                    'dueTasks' => 0,
                    'staleWorkstations' => 0,
                ],
            ];
        }

        $role = $request->session()->get('role');
        $requestedFacilityId = $request->get('facility_id');
        $facilityId = $role === 'super'
            ? ($requestedFacilityId !== null && $requestedFacilityId !== '' ? $requestedFacilityId : null)
            : $user->facility_id;

        $baseDisplays = Display::query()
            ->when($facilityId, function ($query) use ($facilityId) {
                return $query->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                    ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                    ->where('workgroups.facility_id', '=', $facilityId);
            })
            ->join('display_preferences', 'display_preferences.display_id', '=', 'displays.id')
            ->where([
                'display_preferences.name' => 'exclude',
                'display_preferences.value' => '0',
            ]);

        $displaysOk = (clone $baseDisplays)->where('displays.status', 1)->count();
        $displaysFailed = (clone $baseDisplays)->where('displays.status', 2)->count();

        $workstations = Workstation::query()
            ->when($facilityId, function ($query) use ($facilityId) {
                return $query->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                    ->where('workgroups.facility_id', '=', $facilityId);
            })
            ->count();

        $staleThreshold = now()->subDays(7);
        $staleWorkstations = Workstation::query()
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->when($facilityId, fn ($query) => $query->where('workgroups.facility_id', '=', $facilityId))
            ->where(function ($query) use ($staleThreshold) {
                $query->whereNull('workstations.last_connected')
                    ->orWhere('workstations.last_connected', '<', $staleThreshold);
            })
            ->count();

        $tasksCount = Task::query()
            ->where('tasks.deleted', 0)
            ->where('tasks.disabled', 0)
            ->where('tasks.nextrun', '>', 0)
            ->whereHas('display.preferences', function ($query) {
                $query->where('name', 'exclude')->where('value', '0');
            })
            ->when($facilityId, function ($query) use ($facilityId) {
                return $query->whereHas('display.workstation.workgroup', fn ($scope) => $scope->where('facility_id', $facilityId));
            })
            ->count();

        $qaTasksCount = QATask::query()
            ->where('qa_tasks.deleted', 0)
            ->where('qa_tasks.nextdate', '>', 0)
            ->whereHas('display.preferences', function ($query) {
                $query->where('name', 'exclude')->where('value', '0');
            })
            ->when($facilityId, function ($query) use ($facilityId) {
                return $query->whereHas('display.workstation.workgroup', fn ($scope) => $scope->where('facility_id', $facilityId));
            })
            ->count();

        return [
            'dashboardSummary' => [
                'displaysOk' => $displaysOk,
                'displaysFailed' => $displaysFailed,
                'workstations' => $workstations,
                'dueTasks' => $tasksCount + $qaTasksCount,
                'staleWorkstations' => $staleWorkstations,
            ],
        ];
    }

    protected function displayFilters(Request $request, ?User $user): array
    {
        if (!$user) {
            return [
                'canChooseFacility' => false,
                'facilities' => [],
                'workgroupsByFacility' => [],
                'workstationsByWorkgroup' => [],
                'selectedFacilityId' => '',
                'selectedWorkgroupId' => '',
                'selectedWorkstationId' => '',
            ];
        }

        $role = $request->session()->get('role');

        $facilities = Facility::query()
            ->when($role !== 'super', function ($query) use ($user) {
                return $query->where('id', $user->facility_id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        $workgroupsByFacility = Workgroup::query()
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

        $workstationsByWorkgroup = Workstation::query()
            ->when($role !== 'super', function ($query) use ($user) {
                return $query->whereHas('workgroup', function ($groupQuery) use ($user) {
                    $groupQuery->where('facility_id', $user->facility_id);
                });
            })
            ->orderBy('name')
            ->get(['id', 'name', 'workgroup_id'])
            ->groupBy('workgroup_id')
            ->map(function ($items) {
                return $items->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])->values();
            });

        return [
            'canChooseFacility' => $role === 'super',
            'facilities' => $facilities->map(fn ($facility) => [
                'id' => $facility->id,
                'name' => $facility->name,
            ])->values(),
            'workgroupsByFacility' => $workgroupsByFacility,
            'workstationsByWorkgroup' => $workstationsByWorkgroup,
            'selectedFacilityId' => $role === 'super'
                ? (string) $request->get('facility_id', '')
                : (string) $user->facility_id,
            'selectedWorkgroupId' => (string) $request->get('workgroup_id', ''),
            'selectedWorkstationId' => (string) $request->get('workstation_id', ''),
        ];
    }

    protected function facilityFilters(Request $request, ?User $user): array
    {
        if (!$user) {
            return [
                'canChooseFacility' => false,
                'facilities' => [],
                'selectedFacilityId' => '',
                'selectedFacilityName' => '',
            ];
        }

        $role = $request->session()->get('role');
        $facilities = Facility::query()
            ->when($role !== 'super', function ($query) use ($user) {
                return $query->where('id', $user->facility_id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        $selectedFacilityId = $role === 'super'
            ? (string) $request->get('facility_id', '')
            : (string) $user->facility_id;

        $selectedFacilityName = $selectedFacilityId !== ''
            ? (string) $facilities->firstWhere('id', (int) $selectedFacilityId)?->name
            : '';

        return [
            'canChooseFacility' => $role === 'super' && $facilities->count() > 1,
            'facilities' => $facilities->map(fn ($facility) => [
                'id' => (string) $facility->id,
                'name' => $facility->name,
            ])->values(),
            'selectedFacilityId' => $selectedFacilityId,
            'selectedFacilityName' => $selectedFacilityName,
        ];
    }

    protected function workstationFilters(Request $request, ?User $user): array
    {
        if (!$user) {
            return [
                'canChooseFacility' => false,
                'facilities' => [],
                'workgroupsByFacility' => [],
                'selectedFacilityId' => '',
                'selectedFacilityName' => '',
                'selectedWorkgroupId' => '',
                'selectedWorkgroupName' => '',
            ];
        }

        $role = $request->session()->get('role');
        $facilities = Facility::query()
            ->when($role !== 'super', function ($query) use ($user) {
                return $query->where('id', $user->facility_id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        $workgroups = Workgroup::query()
            ->when($role !== 'super', function ($query) use ($user) {
                return $query->where('facility_id', $user->facility_id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'facility_id']);

        $workgroupsByFacility = $workgroups
            ->groupBy('facility_id')
            ->map(function ($items) {
                return $items->map(fn ($item) => [
                    'id' => (string) $item->id,
                    'name' => $item->name,
                ])->values();
            });

        $selectedFacilityId = $role === 'super'
            ? (string) $request->get('facility_id', '')
            : (string) $user->facility_id;
        $selectedWorkgroupId = (string) $request->get('workgroup_id', '');

        if ($selectedFacilityId === '' && $selectedWorkgroupId !== '') {
            $derivedFacilityId = (string) $workgroups->firstWhere('id', (int) $selectedWorkgroupId)?->facility_id;
            if ($derivedFacilityId !== '') {
                $selectedFacilityId = $derivedFacilityId;
            }
        }

        $selectedFacilityName = $selectedFacilityId !== ''
            ? (string) ($request->get('facility_name')
                ?: $facilities->firstWhere('id', (int) $selectedFacilityId)?->name
                ?: '')
            : '';

        $selectedWorkgroupName = $selectedWorkgroupId !== ''
            ? (string) ($request->get('workgroup_name')
                ?: $workgroups->firstWhere('id', (int) $selectedWorkgroupId)?->name
                ?: '')
            : '';

        return [
            'canChooseFacility' => $role === 'super' && $facilities->count() > 1,
            'facilities' => $facilities->map(fn ($facility) => [
                'id' => (string) $facility->id,
                'name' => $facility->name,
            ])->values(),
            'workgroupsByFacility' => $workgroupsByFacility,
            'selectedFacilityId' => $selectedFacilityId,
            'selectedFacilityName' => $selectedFacilityName,
            'selectedWorkgroupId' => $selectedWorkgroupId,
            'selectedWorkgroupName' => $selectedWorkgroupName,
        ];
    }

    protected function safeMobileReturnUrl(Request $request, ?string $fallback = null): ?string
    {
        $returnTo = $request->query('return_to');

        if (!is_string($returnTo) || trim($returnTo) === '') {
            return $fallback;
        }

        $returnTo = trim($returnTo);

        if (str_starts_with($returnTo, '/m/')) {
            return $returnTo;
        }

        $parsed = parse_url($returnTo);
        if (!is_array($parsed)) {
            return $fallback;
        }

        $path = (string) ($parsed['path'] ?? '');
        if (!str_starts_with($path, '/m/')) {
            return $fallback;
        }

        $query = isset($parsed['query']) && $parsed['query'] !== '' ? ('?' . $parsed['query']) : '';

        return $path . $query;
    }

    protected function scopedFacilityId(Request $request, ?User $user): mixed
    {
        if (!$user) {
            return null;
        }

        $role = $request->session()->get('role');
        if ($role === 'super') {
            return null;
        }

        return $user->facility_id;
    }

    protected function workspaceSummary(Request $request, ?User $user): array
    {
        $facilityId = $this->scopedFacilityId($request, $user);
        $dashboardSummary = $this->dashboardSummary($request, $user)['dashboardSummary'] ?? [
            'displaysOk' => 0,
            'displaysFailed' => 0,
            'workstations' => 0,
            'dueTasks' => 0,
            'staleWorkstations' => 0,
        ];

        $facilityCount = Facility::query()
            ->when($facilityId, fn ($query) => $query->where('id', $facilityId))
            ->count();

        $workgroupCount = Workgroup::query()
            ->when($facilityId, fn ($query) => $query->where('facility_id', $facilityId))
            ->count();

        $workstationCount = Workstation::query()
            ->when($facilityId, function ($query) use ($facilityId) {
                return $query->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                    ->where('workgroups.facility_id', '=', $facilityId);
            })
            ->count();

        $displayCount = Display::query()
            ->when($facilityId, function ($query) use ($facilityId) {
                return $query->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                    ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                    ->where('workgroups.facility_id', '=', $facilityId);
            })
            ->count();

        $role = $request->session()->get('role');
        $assignedFacilityName = $user?->facility_id
            ? Facility::query()->whereKey($user->facility_id)->value('name')
            : null;

        $scopeLabel = $role === 'super'
            ? 'All facilities'
            : ($assignedFacilityName ?: 'Assigned facility');

        return [
            'workspaceSummary' => [
                'facilities' => $facilityCount,
                'workgroups' => $workgroupCount,
                'workstations' => $workstationCount,
                'displays' => $displayCount,
                'displayAlerts' => $dashboardSummary['displaysFailed'] ?? 0,
                'staleWorkstations' => $dashboardSummary['staleWorkstations'] ?? 0,
                'dueTasks' => $dashboardSummary['dueTasks'] ?? 0,
                'scopeLabel' => $scopeLabel,
            ],
        ];
    }

    public function index(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        return redirect()->route('mobile.dashboard');
    }

    public function dashboard(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        return $this->mobileView($request, 'mobile.screens.dashboard', array_merge([
            'activeTab' => 'dashboard',
            'eyebrow' => '',
            'screenTitle' => 'Dashboard',
            'screenDescription' => '',
            'seamlessHeader' => true,
        ], $this->dashboardSummary($request, $this->resolveUser($request))));
    }

    public function tasks(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        $initialTaskView = $request->query('view') === 'scheduled' ? 'scheduled' : 'due';

        return $this->mobileView($request, 'mobile.screens.tasks', [
            'activeTab' => 'tasks',
            'eyebrow' => 'Task Scheduler',
            'screenTitle' => 'Tasks',
            'screenDescription' => 'Due tasks and scheduled work in one mobile queue.',
            'backUrl' => $this->safeMobileReturnUrl(
                $request,
                $request->query('from') === 'workspace' ? route('mobile.workspace') : null
            ),
            'initialTaskView' => $initialTaskView,
            'initialTaskDisplayId' => (int) $request->query('display_id', 0),
            'initialTaskDisplayName' => trim((string) $request->query('display_name', '')),
        ]);
    }

    public function workspace(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        return $this->mobileView($request, 'mobile.screens.workspace', array_merge([
            'activeTab' => 'workspace',
            'screenTitle' => 'Workspace',
            'screenDescription' => 'Search, browse, and monitor the operational hierarchy.',
            'seamlessHeader' => true,
        ], $this->workspaceSummary($request, $this->resolveUser($request))));
    }

    protected function reportsViewData(Request $request, ?string $backUrl = null): array
    {
        return [
            'activeTab' => 'reports',
            'eyebrow' => 'History & Reports',
            'screenTitle' => 'Reports',
            'screenDescription' => 'Browse calibration history records and scored report summaries.',
            'backUrl' => $backUrl,
        ];
    }

    public function reports(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        return $this->mobileView($request, 'mobile.screens.reports', $this->reportsViewData($request));
    }

    public function histories(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        return $this->mobileView(
            $request,
            'mobile.screens.reports',
            $this->reportsViewData($request, route('mobile.dashboard'))
        );
    }

    public function scheduler(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        return $this->mobileView($request, 'mobile.screens.tasks', [
            'activeTab' => 'tasks',
            'eyebrow' => 'Task Scheduler',
            'screenTitle' => 'Tasks',
            'screenDescription' => 'Due tasks and scheduled work in one mobile queue.',
            'backUrl' => $this->safeMobileReturnUrl($request, route('mobile.dashboard')),
            'initialTaskView' => 'scheduled',
            'initialTaskDisplayId' => (int) $request->query('display_id', 0),
            'initialTaskDisplayName' => trim((string) $request->query('display_name', '')),
        ]);
    }

    public function displays(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        $user = $this->resolveUser($request);

        $backUrl = null;
        if ($request->query('from') === 'workspace') {
            $backUrl = route('mobile.workspace');
        } elseif ($request->query('workstation_id')) {
            $backUrl = route('mobile.workstations', array_filter([
                'facility_id' => $request->query('facility_id'),
                'workgroup_id' => $request->query('workgroup_id'),
                'facility_name' => $request->query('facility_name'),
                'workgroup_name' => $request->query('workgroup_name'),
            ], fn ($value) => $value !== null && $value !== ''));
        } elseif ($request->query('workgroup_id')) {
            $backUrl = route('mobile.workgroups', array_filter([
                'facility_id' => $request->query('facility_id'),
                'facility_name' => $request->query('facility_name'),
            ], fn ($value) => $value !== null && $value !== ''));
        }

        return $this->mobileView($request, 'mobile.screens.displays', [
            'activeTab' => 'workspace',
            'eyebrow' => 'Display Fleet',
            'screenTitle' => 'Displays',
            'screenDescription' => 'Search the fleet and open detail.',
            'backUrl' => $this->safeMobileReturnUrl($request, $backUrl),
            'displayFilters' => $this->displayFilters($request, $user),
        ]);
    }

    public function displayDetail(Request $request, int $id)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        $backUrl = route('mobile.displays', array_filter([
            'facility_id' => $request->query('facility_id'),
            'workgroup_id' => $request->query('workgroup_id'),
            'workstation_id' => $request->query('workstation_id'),
            'facility_name' => $request->query('facility_name'),
            'workgroup_name' => $request->query('workgroup_name'),
            'workstation_name' => $request->query('workstation_name'),
            'status' => $request->query('status'),
            'sort' => $request->query('sort'),
            'order' => $request->query('order'),
            'from' => $request->query('from'),
        ], fn ($value) => $value !== null && $value !== ''));

        if ($request->query('from') === 'workspace' && !$request->query('workstation_id') && !$request->query('workgroup_id') && !$request->query('facility_id')) {
            $backUrl = route('mobile.workspace');
        }

        return $this->mobileView($request, 'mobile.screens.display-detail', [
            'activeTab' => 'workspace',
            'eyebrow' => 'Display Detail',
            'screenTitle' => 'Display Overview',
            'screenDescription' => 'Current health and recent history.',
            'seamlessHeader' => true,
            'displayId' => $id,
            'backUrl' => $this->safeMobileReturnUrl($request, $backUrl),
        ]);
    }

    public function facilities(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        return $this->mobileView($request, 'mobile.screens.facilities', [
            'activeTab' => 'workspace',
            'screenTitle' => 'Facilities',
            'screenDescription' => 'Browse top-level scope and health.',
            'backUrl' => $this->safeMobileReturnUrl($request, $request->query('from') === 'workspace' ? route('mobile.workspace') : null),
        ]);
    }

    public function facilityDetail(Request $request, int $id)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        return $this->mobileView($request, 'mobile.screens.facility-detail', [
            'activeTab' => 'workspace',
            'screenTitle' => 'Facility',
            'screenDescription' => 'Facility scope overview.',
            'seamlessHeader' => true,
            'facilityId' => $id,
            'backUrl' => $this->safeMobileReturnUrl($request, route('mobile.facilities', array_filter([
                'from' => $request->query('from'),
            ], fn ($value) => $value !== null && $value !== ''))),
        ]);
    }

    public function workgroups(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        $backUrl = null;
        if ($request->query('from') === 'workspace') {
            $backUrl = route('mobile.workspace');
        } elseif ($request->query('facility_id')) {
            $backUrl = route('mobile.facilities');
        }

        return $this->mobileView($request, 'mobile.screens.workgroups', [
            'activeTab' => 'workspace',
            'screenTitle' => 'Workgroups',
            'screenDescription' => 'Browse workgroups within scope.',
            'backUrl' => $this->safeMobileReturnUrl($request, $backUrl),
            'facilityFilters' => $this->facilityFilters($request, $this->resolveUser($request)),
        ]);
    }

    public function workgroupDetail(Request $request, int $id)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        $fallback = route('mobile.workgroups', array_filter([
            'facility_id' => $request->query('facility_id'),
            'facility_name' => $request->query('facility_name'),
            'from' => $request->query('from'),
        ], fn ($value) => $value !== null && $value !== ''));

        return $this->mobileView($request, 'mobile.screens.workgroup-detail', [
            'activeTab' => 'workspace',
            'screenTitle' => 'Workgroup',
            'screenDescription' => 'Workgroup scope overview.',
            'seamlessHeader' => true,
            'workgroupId' => $id,
            'backUrl' => $this->safeMobileReturnUrl($request, $fallback),
        ]);
    }

    public function workstations(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        $backUrl = null;
        if ($request->query('from') === 'workspace') {
            $backUrl = route('mobile.workspace');
        } elseif ($request->query('workgroup_id')) {
            $backUrl = route('mobile.workgroups', array_filter([
                'facility_id' => $request->query('facility_id'),
                'facility_name' => $request->query('facility_name'),
            ], fn ($value) => $value !== null && $value !== ''));
        }

        return $this->mobileView($request, 'mobile.screens.workstations', [
            'activeTab' => 'workspace',
            'screenTitle' => 'Workstations',
            'screenDescription' => 'Browse endpoint health and sync.',
            'backUrl' => $this->safeMobileReturnUrl($request, $backUrl),
            'workstationFilters' => $this->workstationFilters($request, $this->resolveUser($request)),
        ]);
    }

    public function workstationDetail(Request $request, int $id)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        $fallback = route('mobile.workstations', array_filter([
            'facility_id' => $request->query('facility_id'),
            'workgroup_id' => $request->query('workgroup_id'),
            'facility_name' => $request->query('facility_name'),
            'workgroup_name' => $request->query('workgroup_name'),
            'from' => $request->query('from'),
        ], fn ($value) => $value !== null && $value !== ''));

        return $this->mobileView($request, 'mobile.screens.workstation-detail', [
            'activeTab' => 'workspace',
            'screenTitle' => 'Workstation',
            'screenDescription' => 'Workstation scope overview.',
            'seamlessHeader' => true,
            'workstationId' => $id,
            'backUrl' => $this->safeMobileReturnUrl($request, $fallback),
        ]);
    }

    protected function alertsViewData(Request $request, string $initialAlertsView = 'displays', ?string $backUrl = null): array
    {
        $allowed = ['displays', 'connections', 'inbox'];

        return array_merge([
            'activeTab' => 'alerts',
            'eyebrow' => 'Alerts',
            'screenTitle' => 'Alerts',
            'screenDescription' => 'Displays that need attention, connection watchlist, and unread notifications in one place.',
            'backUrl' => $backUrl,
            'initialAlertsView' => in_array($initialAlertsView, $allowed, true) ? $initialAlertsView : 'displays',
        ], $this->dashboardSummary($request, $this->resolveUser($request)));
    }

    public function alerts(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        return $this->mobileView(
            $request,
            'mobile.screens.alerts',
            $this->alertsViewData($request, (string) $request->query('panel', 'displays'))
        );
    }

    public function notifications(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        return $this->mobileView(
            $request,
            'mobile.screens.alerts',
            $this->alertsViewData($request, 'inbox', route('mobile.dashboard'))
        );
    }

    public function search(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        return redirect()->route('mobile.workspace');
    }

    public function profile(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        return $this->mobileView($request, 'mobile.screens.profile', [
            'activeTab' => 'profile',
            'eyebrow' => 'Account',
            'screenTitle' => 'Profile',
            'screenDescription' => 'Account and workspace context.',
        ]);
    }

    public function profileSettings(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return $redirect;
        }

        $user = $this->resolveUser($request);
        if (!$user) {
            return redirect()->route('mobile.login');
        }

        if ($request->isMethod('post')) {
            if ($request->input('username') !== null) {
                $username = (string) $request->input('username');
                $pass = (string) $request->input('password');
                $pass2 = (string) $request->input('password2');
                $fullname = (string) $request->input('fname');
                $email = (string) $request->input('email');

                if ($request->file('profile_image')) {
                    $file = $request->file('profile_image');
                    $destinationPath = public_path('assets/images/profile-images');
                    $extension = $file->getClientOriginalExtension();
                    $fileName = (string) Str::uuid() . '.' . $extension;

                    if (!is_dir($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    if ($file->move($destinationPath, $fileName)) {
                        $featuredImage = 'assets/images/profile-images/' . $fileName;
                        $oldProfileImage = $user->profile_image;

                        if ($oldProfileImage && str_starts_with($oldProfileImage, 'assets/images/profile-images/')) {
                            $oldProfileImagePath = public_path($oldProfileImage);
                            if (is_file($oldProfileImagePath)) {
                                @unlink($oldProfileImagePath);
                            }
                        }

                        User::where('id', $user->id)->update([
                            'profile_image' => $featuredImage,
                        ]);

                        $user->profile_image = $featuredImage;
                    }
                }

                User::where('id', $user->id)->update([
                    'name' => $username,
                    'fullname' => $fullname,
                    'email' => $email,
                ]);

                if ($pass !== '') {
                    if ($pass !== $pass2) {
                        $request->session()->flash('error', 'Passwords did not match.');
                        return redirect()->route('mobile.profile.settings');
                    }

                    User::where('id', $user->id)->update([
                        'password' => Hash::make($pass),
                    ]);
                }

                $request->session()->flash('success', 'Profile details updated successfully.');
                return redirect()->route('mobile.profile.settings');
            }

            if ($request->input('remote_user') !== null) {
                $remoteUser = (string) $request->input('remote_user');
                $remotePassword = (string) $request->input('remote_password');

                User::where('id', $user->id)->update([
                    'sync_user' => $remoteUser,
                    'sync_password_raw' => $remotePassword,
                    'sync_password' => md5($remotePassword),
                ]);

                $request->session()->flash('success', 'Credentials updated successfully.');
                return redirect()->route('mobile.profile.settings');
            }
        }

        return $this->mobileView($request, 'mobile.screens.profile-settings', [
            'activeTab' => 'profile',
            'eyebrow' => 'Account',
            'screenTitle' => 'Profile Settings',
            'screenDescription' => 'Edit account details and remote credentials.',
            'backUrl' => route('mobile.profile'),
        ]);
    }

    public function profileRemoveImage(Request $request)
    {
        if ($redirect = $this->redirectIfNotReady($request)) {
            return response()->json(['success' => 0], 401);
        }

        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json(['success' => 0], 401);
        }

        $profileImage = $user->profile_image;
        if ($profileImage && str_starts_with($profileImage, 'assets/images/profile-images/')) {
            $profileImagePath = public_path($profileImage);
            if (is_file($profileImagePath)) {
                @unlink($profileImagePath);
            }
        }

        User::where('id', $user->id)->update([
            'profile_image' => null,
        ]);

        return response()->json(['success' => 1]);
    }
}
