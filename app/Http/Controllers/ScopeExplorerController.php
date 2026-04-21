<?php

namespace App\Http\Controllers;

use App\Models\Display;
use App\Models\Facility;
use App\Models\User;
use App\Models\Workgroup;
use App\Models\Workstation;
use Illuminate\Http\Request;

class ScopeExplorerController extends Controller
{
    private function requireSuperAdmin(Request $request): void
    {
        $user = User::find($request->session()->get('id'));
        abort_unless($user && $user->hasRole('super'), 403);
    }

    public function index(Request $request)
    {
        $this->requireSuperAdmin($request);

        return view('settings.scope_explorer', [
            'title' => 'Scope Explorer',
        ]);
    }

    public function facilities(Request $request)
    {
        $this->requireSuperAdmin($request);

        $facilities = Facility::query()
            ->withCount(['workgroups', 'workstations', 'displays'])
            ->orderBy('name')
            ->get(['id', 'name', 'location', 'timezone'])
            ->map(fn (Facility $facility) => $this->facilityPayload($facility))
            ->values();

        return response()->json([
            'items' => $facilities,
            'summary' => [
                'facilities' => $facilities->count(),
                'workgroups' => Workgroup::count(),
                'workstations' => Workstation::count(),
                'displays' => Display::count(),
            ],
        ]);
    }

    public function children(Request $request, string $type, int $id)
    {
        $this->requireSuperAdmin($request);

        $items = match ($type) {
            'facility' => Workgroup::query()
                ->where('facility_id', $id)
                ->withCount(['workstations', 'displays'])
                ->orderBy('name')
                ->get(['id', 'facility_id', 'name', 'address', 'phone'])
                ->map(fn (Workgroup $workgroup) => $this->workgroupPayload($workgroup))
                ->values(),
            'workgroup' => Workstation::query()
                ->where('workgroup_id', $id)
                ->withCount('displays')
                ->orderBy('name')
                ->get(['id', 'workgroup_id', 'name', 'last_connected'])
                ->map(fn (Workstation $workstation) => $this->workstationPayload($workstation))
                ->values(),
            'workstation' => Display::query()
                ->where('workstation_id', $id)
                ->orderBy('manufacturer')
                ->orderBy('model')
                ->orderBy('serial')
                ->get(['id', 'workstation_id', 'manufacturer', 'model', 'serial', 'status'])
                ->map(fn (Display $display) => $this->displayPayload($display))
                ->values(),
            default => abort(404),
        };

        return response()->json(['items' => $items]);
    }

    private function facilityPayload(Facility $facility): array
    {
        return [
            'id' => (int) $facility->id,
            'type' => 'facility',
            'name' => $facility->name ?: ('Facility #' . $facility->id),
            'subtitle' => trim(collect([$facility->location, $facility->timezone])->filter()->implode(' - ')) ?: 'Facility scope',
            'meta' => [
                'Workgroups' => (int) ($facility->workgroups_count ?? 0),
                'Workstations' => (int) ($facility->workstations_count ?? 0),
                'Displays' => (int) ($facility->displays_count ?? 0),
            ],
            'childLabel' => 'workgroups',
            'childCount' => (int) ($facility->workgroups_count ?? 0),
            'urls' => [
                'open' => url('facility-info/' . $facility->id),
                'edit' => url('facility-info/' . $facility->id),
                'settings' => url('global-settings'),
            ],
            'deleteEndpoint' => url('delete-facility'),
        ];
    }

    private function workgroupPayload(Workgroup $workgroup): array
    {
        return [
            'id' => (int) $workgroup->id,
            'type' => 'workgroup',
            'name' => $workgroup->name ?: ('Workgroup #' . $workgroup->id),
            'subtitle' => trim(collect([$workgroup->address, $workgroup->phone])->filter()->implode(' - ')) ?: 'Workgroup folder',
            'meta' => [
                'Workstations' => (int) ($workgroup->workstations_count ?? 0),
                'Displays' => (int) ($workgroup->displays_count ?? 0),
            ],
            'childLabel' => 'workstations',
            'childCount' => (int) ($workgroup->workstations_count ?? 0),
            'urls' => [
                'open' => url('workgroups-info/' . $workgroup->id),
                'edit' => url('workgroups-info/' . $workgroup->id),
                'settings' => url('global-settings'),
            ],
            'deleteEndpoint' => url('delete-workgroup'),
        ];
    }

    private function workstationPayload(Workstation $workstation): array
    {
        return [
            'id' => (int) $workstation->id,
            'type' => 'workstation',
            'name' => $workstation->name ?: ('Workstation #' . $workstation->id),
            'subtitle' => $workstation->last_connected ? ('Last sync ' . $workstation->last_connected) : 'No sync timestamp',
            'meta' => [
                'Displays' => (int) ($workstation->displays_count ?? 0),
            ],
            'childLabel' => 'displays',
            'childCount' => (int) ($workstation->displays_count ?? 0),
            'urls' => [
                'open' => url('workstations-info/' . $workstation->id),
                'edit' => url('workstations-info/' . $workstation->id),
                'settings' => url('application-settings/' . $workstation->id),
            ],
            'deleteEndpoint' => url('delete-workstation'),
        ];
    }

    private function displayPayload(Display $display): array
    {
        return [
            'id' => (int) $display->id,
            'type' => 'display',
            'name' => $display->treetext ?: ('Display #' . $display->id),
            'subtitle' => (int) $display->status === Display::STATUS_OK ? 'Healthy display' : 'Needs attention',
            'meta' => [
                'Status' => (int) $display->status === Display::STATUS_OK ? 'OK' : 'Failed',
            ],
            'childLabel' => 'items',
            'childCount' => 0,
            'urls' => [
                'open' => url('display-settings/' . $display->id),
                'edit' => url('display-settings/' . $display->id),
                'settings' => url('display-settings/' . $display->id),
            ],
            'deleteEndpoint' => url('delete-display'),
        ];
    }
}
