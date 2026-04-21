<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\DisplayCalibrationExport;
use App\Exports\AllDisplaysExport;
use App\Exports\WorkgroupsExport;
use App\Exports\WorkstationsExport;
use App\Exports\AllTasksExport;
use App\Exports\HistoriesReportsExport;
use Carbon\Carbon;
use DB;
use Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Schema;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class ReportsController extends Controller
{
    protected function loadSiteSettings(): array
    {
        if (!Schema::hasTable('settings')) {
            return [
                'Site logo' => public_path('assets/images/perfectlum-logo.png'),
            ];
        }

        $settings = \App\Models\Setting::pluck('value', 'title')->toArray();

        if (empty($settings['Site logo'])) {
            $settings['Site logo'] = public_path('assets/images/perfectlum-logo.png');
        }

        return $settings;
    }

    public function exportDisplayCalibration(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);

        $from = $request->input('date_from');
        $to = $request->input('date_to');
        $export_type = $request->input('export_type');

        $ids = request()->input('id') ? explode(',', request()->input('id')) : [];
        $task = \App\Models\Task::with(['ScheduleType', 'taskType', 'display.workstation.workgroup.facility'])
            ->where(['type' => 'cal', 'deleted' => 0])
            ->join('displays', 'displays.id', '=', 'tasks.display_id');
        $task->when(count($ids) > 0, function ($q) use ($ids) {
            return $q->whereIn('display_id', $ids);
        });

        $facility_id = $user->facility_id;

        $task->when($facility_id, function ($q) use ($facility_id) {
            return $q->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                ->where('workgroups.facility_id', '=', $facility_id);
        })->select('tasks.*', 'tasks.startdate as startdate1');
        
        $data=$task->get();

        $fileName = 'displaycalibration.xlsx';


        if($export_type == 'pdf'){
            $fileName = substr($fileName,0,strlen($fileName)-5);
            return $this->exportPdf($fileName, $data, 'reports.display_calibration_pdf');
        }

        // die(json_encode($data));
        $site=$this->loadSiteSettings();
        return Excel::download(new DisplayCalibrationExport($data, $from, $to, $site), $fileName);
    }

    public function exportDisplays(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);

        $from = $request->input('date_from');
        $to = $request->input('date_to');
        $export_type = $request->input('export_type');
        $type=$request->input('type');
        if($type=='failed') $type=2;
        elseif($type=='ok') $type=1;

        $facility_id = $user->facility_id;
        $workstation_id = request('workstation_id')?request('workstation_id'):'';
        $items = \App\Models\Display::with('workstation.workgroup.facility');

        if($type)
        {
            $items->where('status', $type);
        }

        $items->when($workstation_id, function($q) use ($workstation_id) {
            return $q->where('workstation_id','=',$workstation_id);
        });

        $facility_id = $user->facility_id;
        $items->when($facility_id, function($q) use ($facility_id) {
            return $q->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                     ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                     ->where('workgroups.facility_id','=',$facility_id);
        })->select('displays.*');
        $data = $items->get();

        $fileName = 'displays.xlsx';


        if($export_type == 'pdf'){
            $fileName = substr($fileName,0,strlen($fileName)-5);
            return $this->exportPdf($fileName, $data, 'reports.displays_pdf');
        }

        // die(json_encode($data));
        $site=$this->loadSiteSettings();
        return Excel::download(new AllDisplaysExport($data, $from, $to, $site), $fileName);
    }

    public function exportWorkgroups(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $userRole=$request->session()->get('role');

        $from = $request->input('date_from');
        $to = $request->input('date_to');
        $export_type = $request->input('export_type');

        $facility_id = $userRole === 'super'
            ? (request('facility_id') ? request('facility_id') : $user->facility_id)
            : $user->facility_id;
        $items = \App\Models\Workgroup::with('facility');

        $items->when($facility_id, function($q) use ($facility_id) {
            return $q->where('facility_id','=',$facility_id);
        })->select('workgroups.*');
        $data = $items->get();

        $fileName = 'workgroups.xlsx';


        if($export_type == 'pdf'){
            $fileName = substr($fileName,0,strlen($fileName)-5);
            return $this->exportPdf($fileName, $data, 'reports.workgroups_pdf');
        }

        // die(json_encode($data));
        $site=$this->loadSiteSettings();
        return Excel::download(new WorkgroupsExport($data, $from, $to, $site), $fileName);
    }

    public function exportWorkstations(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $userRole=$request->session()->get('role');

        $from = $request->input('date_from');
        $to = $request->input('date_to');
        $export_type = $request->input('export_type');

        $workgroup_id = request('workgroup_id')?request('workgroup_id'):'';
        $items = \App\Models\Workstation::with('workgroup.facility');

        $items->when($workgroup_id, function($q) use ($workgroup_id) {
            return $q->where('workgroup_id','=',$workgroup_id);
        });
        
        if ($userRole!='super') {
            $facility_id = $user->facility_id;
            $items->when($facility_id, function($q) use ($facility_id) {
                return $q->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
                         ->where('workgroups.facility_id','=',$facility_id);
            })->select('workstations.*');
        }

        $data = $items->get();

        $fileName = 'workstations.xlsx';


        if($export_type == 'pdf'){
            $fileName = substr($fileName,0,strlen($fileName)-5);
            return $this->exportPdf($fileName, $data, 'reports.workstations_pdf');
        }

        // die(json_encode($data));
        $site=$this->loadSiteSettings();
        return Excel::download(new WorkstationsExport($data, $from, $to, $site), $fileName);
    }

    public function exportAllTasks(Request $request)
    {
        ini_set('memory_limit', '5120M');
        ini_set('max_execution_time', -1);

        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);

        $from = $request->input('date_from');
        $to = $request->input('date_to');
        $export_type = $request->input('export_type');

        $data = array();

        $facility_id = $user->facility_id;

        $ids = request()->input('id') ? explode(',', request()->input('id')) : [];

        // only get task displayed on client (lastrun>0)
        $task = \App\Models\Task::with(['display'], ['display.workstation.workgroup.facility'])
            ->join('task_types', 'tasks.type', '=', 'task_types.key')
            ->join('schedule_types', 'tasks.schtype', '=', 'schedule_types.client_id')
            ->join('displays', 'displays.id', '=', 'tasks.display_id')
            ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->where(['deleted' => 0, 'disabled' => 0])
            // ->where('lastrun', '>', 0)
            ->where('nextrun', '>', 0);

        $task->when(count($ids) > 0, function ($q) use ($ids) {
            return $q->whereIn('display_id', $ids);
        });

        $task->when($facility_id, function ($q) use ($facility_id) {
            return $q->where('workgroups.facility_id', '=', $facility_id);
        })->select(DB::raw('"task" as type, tasks.id as id, task_types.title as name, schedule_types.title as schtype, 
                                displays.id as d_id, 
                                displays.model as display_model,
        workstations.name as workstation_name,
        workgroups.name as workgroup_name,
        facilities.name as facility_name,
                                workstations.id as ws_id, 
                                workgroups.id as wg_id, 
                                facilities.id as f_id,
                                tasks.startdate as startdate1,
                                tasks.nextrun as nextrun,
                                (case 
                                        when tasks.schtype=0 THEN \'9999-12-31\'
                                        when (tasks.startdate IS NULL OR tasks.nextrun IS NULL OR tasks.nextrun=0) THEN 0
                                        else FROM_UNIXTIME(tasks.nextrun)
                                    end) as due_date_sort'));
        // Get QA tasks
        $qatasks = \App\Models\QATask::join('displays', 'displays.id', '=', 'qa_tasks.display_id')
            ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
            ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')
            ->join('facilities', 'facilities.id', '=', 'workgroups.facility_id')
            ->where('nextdate', '>', 0)
            ->where(['deleted' => 0]);
        $qatasks->when(count($ids) > 0, function ($q) use ($ids) {
            return $q->whereIn('display_id', $ids);
        });

        $qatasks->when($facility_id, function ($q) use ($facility_id) {
            return $q->where('workgroups.facility_id', '=', $facility_id);
        })->select(DB::raw('"qa_task" as type,qa_tasks.id as id, qa_tasks.name as name, qa_tasks.freq as schtype, 
        displays.model as display_model,
        workstations.name as workstation_name,
        workgroups.name as workgroup_name,
        facilities.name as facility_name,
                                displays.id as d_id, 
                                workstations.id as ws_id, 
                                workgroups.id as wg_id, 
                                facilities.id as f_id,
                                "2019-08-27 00:00" as startdate1,
                                nextdate as nextrun,
                                nextdate as due_date_sort'));
                                
        $tasks = $task->unionAll($qatasks)->get();
        
        $data = $tasks->map(function ($t) {
    return [
        'type'       => $t->type,
        'id'         => $t->id,
        'name'       => $t->name,
        'schtype'    => $t->schtype,
        'startdate1' => $t->startdate1,
        'nextrun'    => $t->nextrun,
        'due_date_sort' => $t->due_date_sort,
        'display'    => $t->display, // Already loaded, no need to query again
        'display_model' => $t->display_model,
        'workstation' => $t->workstation_name, 
        'workgroup'  => $t->workgroup_name,
        'facility'   => $t->facility_name,
        'duedate'    => $t->type == 'qa_task' ? $t->duedate : $t->due_date_sort,
    ];
});

        $fileName = 'scheduleTasks.xlsx';


        if($export_type == 'pdf')
        {
            $fileName = substr($fileName,0,strlen($fileName)-5);
            return $this->exportPdf($fileName, $data, 'reports.all_tasks_pdf');
        }

        // die(json_encode($data));
        $site=$this->loadSiteSettings();
        return Excel::download(new AllTasksExport($data, $from, $to, $site), $fileName);
    }

    public function exportHistoriesReports(Request $request)
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', -1);
        
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role=$request->session()->get('role');

        $from = $request->input('date_from');
        $to = $request->input('date_to');
        $export_type = $request->input('export_type');

        $facility_id = $user->facility_id;

        $id = request()->input('id');
        $s = explode('-', $id);
        $type = $id = '';
        if (count($s) >= 2) {
            $type = $s[0];
            $id = $s[1];
        }

        $items = \App\Models\History::with('display.workstation.workgroup.facility');
        $items->when($id, function ($q) use ($id) {
            return $q->where('display_id', $id);
        });

        if ($role!='super') { // load current facility only
        $items->when($facility_id, function ($q) use ($facility_id) {
            return $q->join('displays', 'displays.id', '=', 'histories.display_id')
                ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id')

                ->where('workgroups.facility_id', '=', $facility_id);
        })->select('histories.*');
        }
        else
        {
            $facility_id=1;
            $items->when($facility_id, function ($q) use ($facility_id) {
            return $q->join('displays', 'displays.id', '=', 'histories.display_id')
                ->join('workstations', 'workstations.id', '=', 'displays.workstation_id')
                ->join('workgroups', 'workgroups.id', '=', 'workstations.workgroup_id');
            })->select('histories.*');
        }
        
        $items->orderBy('time', 'DESC');

        $data=$items->get();
        //echo count($data); exit();

        $fileName = 'historiesReports.xlsx';


        if($export_type == 'pdf')
        {
            $fileName = substr($fileName,0,strlen($fileName)-5);
            return $this->exportPdf($fileName, $data, 'reports.histories_reports_pdf');
        }

        // die(json_encode($data));
        $site=$this->loadSiteSettings();
        return Excel::download(new HistoriesReportsExport($data, $from, $to, $site), $fileName);
    }

    public function exportPdf($name, $data, $view)
    {
        $report = $this->buildTablePdfReport($view, $data);
        $tempDir = storage_path('app/mpdf-temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $html = view('reports.table_pdf', [
            'report' => $report,
            'logoPerfectLum' => $this->embedPublicImage('assets/images/perfectlum-logo.png'),
            'logoQubyx' => $this->embedPublicImage('assets/images/qubyx-black.png'),
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 14,
            'margin_bottom' => 16,
            'margin_header' => 0,
            'margin_footer' => 8,
            'default_font' => 'dejavusans',
            'tempDir' => $tempDir,
        ]);

        $title = $report['title'] ?? $name;
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor('PerfectLum');
        $mpdf->SetDisplayMode('fullwidth');
        $mpdf->SetAutoPageBreak(true, 16);
        $mpdf->showImageErrors = false;
        $mpdf->SetHTMLFooter('
            <div style="border-top:1px solid #d7dee8; padding-top:6px; font-size:9px; color:#667085; letter-spacing:0.12em; text-transform:uppercase;">
                <table width="100%" style="border-collapse:collapse;">
                    <tr>
                        <td style="font-size:9px; color:#667085;">PerfectLum · qubyx.com</td>
                        <td align="right" style="font-size:9px; color:#667085;">Page {PAGENO} of {nbpg}</td>
                    </tr>
                </table>
            </div>
        ');

        $previousErrorReporting = error_reporting();
        $previousDisplayErrors = ini_get('display_errors');
        error_reporting($previousErrorReporting & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);
        ini_set('display_errors', '0');

        try {
            $mpdf->WriteHTML($html);
            $filename = preg_replace('/[^A-Za-z0-9\\-_]+/', '-', $name ?: 'report') . '.pdf';

            return response($mpdf->Output($filename, Destination::STRING_RETURN), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            ]);
        } finally {
            error_reporting($previousErrorReporting);
            ini_set('display_errors', $previousDisplayErrors === false ? '0' : (string) $previousDisplayErrors);
        }
    }

    private function buildTablePdfReport(string $view, $data): array
    {
        $rows = collect($data);

        return match ($view) {
            'reports.displays_pdf' => [
                'eyebrow' => 'Inventory Report',
                'title' => 'All Connected Displays',
                'subtitle' => 'Connected display inventory exported from the current workspace scope.',
                'generatedAt' => now()->format('d M Y H:i'),
                'total' => $rows->count(),
                'columns' => ['Display', 'Inventory Number', 'Workstation', 'Workgroup', 'Facility', 'Status'],
                'rows' => $rows->map(fn($display) => [
                    $this->displayName($display),
                    $this->preferenceValue($display, 'InventoryNumber'),
                    optional($display->workstation)->name ?: '-',
                    optional(optional($display->workstation)->workgroup)->name ?: '-',
                    optional(optional(optional($display->workstation)->workgroup)->facility)->name ?: '-',
                    $this->statusCell((int) ($display->status ?? 0) === 1 ? 'OK' : 'Failed'),
                ])->values(),
            ],
            'reports.workgroups_pdf' => [
                'eyebrow' => 'Workspace Report',
                'title' => 'Workgroups',
                'subtitle' => 'Department and workgroup list exported from the current workspace scope.',
                'generatedAt' => now()->format('d M Y H:i'),
                'total' => $rows->count(),
                'columns' => ['Name', 'Address', 'Phone', 'Facility'],
                'rows' => $rows->map(fn($workgroup) => [
                    $workgroup->name ?? '-',
                    $workgroup->address ?? '-',
                    $workgroup->phone ?? '-',
                    optional($workgroup->facility)->name ?: '-',
                ])->values(),
            ],
            'reports.workstations_pdf' => [
                'eyebrow' => 'Hardware Report',
                'title' => 'Workstations',
                'subtitle' => 'Client workstation list exported from the current workspace scope.',
                'generatedAt' => now()->format('d M Y H:i'),
                'total' => $rows->count(),
                'columns' => ['Name', 'Workgroup', 'Facility', 'Sleep Time', 'Displays'],
                'rows' => $rows->map(fn($workstation) => [
                    $workstation->name ?? '-',
                    optional($workstation->workgroup)->name ?: '-',
                    optional(optional($workstation->workgroup)->facility)->name ?: '-',
                    $workstation->sleep_time ?? '-',
                    method_exists($workstation, 'displays') ? $workstation->displays()->count() : '-',
                ])->values(),
            ],
            'reports.display_calibration_pdf' => [
                'eyebrow' => 'Scheduler Report',
                'title' => 'Display Calibration Schedules',
                'subtitle' => 'Calibration tasks exported from the selected display scope.',
                'generatedAt' => now()->format('d M Y H:i'),
                'total' => $rows->count(),
                'columns' => ['Display', 'Workstation', 'Workgroup', 'Facility', 'Task Type', 'Schedule Type', 'Due Date'],
                'rows' => $rows->map(fn($task) => [
                    $this->displayName($task->display),
                    optional(optional($task->display)->workstation)->name ?: '-',
                    optional(optional(optional($task->display)->workstation)->workgroup)->name ?: '-',
                    optional(optional(optional(optional($task->display)->workstation)->workgroup)->facility)->name ?: '-',
                    optional($task->taskType)->title ?: ($task->type ?? '-'),
                    optional($task->ScheduleType)->title ?: ($task->schtype ?? '-'),
                    $this->dueDateForTask($task),
                ])->values(),
            ],
            'reports.all_tasks_pdf' => [
                'eyebrow' => 'Scheduler Report',
                'title' => 'All Calibration and QA Schedules',
                'subtitle' => 'Scheduled task list exported from the current workspace scope.',
                'generatedAt' => now()->format('d M Y H:i'),
                'total' => $rows->count(),
                'columns' => ['Display', 'Workstation', 'Workgroup', 'Facility', 'Task Type', 'Schedule Type', 'Due Date'],
                'rows' => $rows->map(fn($task) => [
                    $task['display_model'] ?? '-',
                    $task['workstation'] ?? '-',
                    $task['workgroup'] ?? '-',
                    $task['facility'] ?? '-',
                    $task['name'] ?? '-',
                    $task['schtype'] ?? '-',
                    $this->formatReportDate($task['duedate'] ?? ($task['due_date_sort'] ?? null)),
                ])->values(),
            ],
            'reports.histories_reports_pdf' => [
                'eyebrow' => 'History Report',
                'title' => 'History & Reports',
                'subtitle' => 'Completed calibration and QA history exported from the current workspace scope.',
                'generatedAt' => now()->format('d M Y H:i'),
                'total' => $rows->count(),
                'columns' => ['Task Name', 'Pattern', 'Display', 'Workstation', 'Workgroup', 'Performed Date/Time', 'Result'],
                'rows' => $rows->map(fn($history) => [
                    $history->name ?? '-',
                    $history->regulation ?? '-',
                    $this->displayName($history->display),
                    optional(optional($history->display)->workstation)->name ?: '-',
                    optional(optional(optional($history->display)->workstation)->workgroup)->name ?: '-',
                    $this->formatHistoryTime($history),
                    $this->statusCell($history->result_desc ?: '-'),
                ])->values(),
            ],
            default => [
                'eyebrow' => 'PerfectLum Report',
                'title' => 'Report',
                'subtitle' => 'Exported workspace report.',
                'generatedAt' => now()->format('d M Y H:i'),
                'total' => $rows->count(),
                'columns' => ['Name'],
                'rows' => $rows->map(fn($row) => [$this->plainValue($row->name ?? '-')])->values(),
            ],
        };
    }

    private function displayName($display): string
    {
        if (!$display) {
            return '-';
        }

        $manufacturer = trim((string) ($display->manufacturer ?? $this->preferenceValue($display, 'Manufacturer', '')));
        $model = trim((string) ($display->model ?? $this->preferenceValue($display, 'Model', '')));
        $serial = trim((string) ($display->serial ?? $this->preferenceValue($display, 'SerialNumber', '')));
        $name = trim($manufacturer . ' ' . $model);

        return trim($name . ($serial !== '' ? ' (' . $serial . ')' : '')) ?: '-';
    }

    private function preferenceValue($model, string $key, string $fallback = '-'): string
    {
        if ($model && method_exists($model, 'preference')) {
            $value = trim((string) $model->preference($key));
            return $value !== '' ? $value : $fallback;
        }

        return $fallback;
    }

    private function statusCell(string $value): array
    {
        $normalized = strtolower($value);
        $tone = str_contains($normalized, 'ok') && !str_contains($normalized, 'not')
            ? 'success'
            : (str_contains($normalized, 'fail') || str_contains($normalized, 'cancel') ? 'danger' : 'neutral');

        return ['value' => $value, 'tone' => $tone];
    }

    private function dueDateForTask($task): string
    {
        if (!$task || empty($task->nextrun) || (int) $task->nextrun <= 0) {
            return 'Never';
        }

        $timezone = optional(optional(optional(optional($task->display)->workstation)->workgroup)->facility)->timezone
            ?: config('app.timezone', 'UTC');

        return Carbon::createFromTimestamp((int) $task->nextrun, $timezone)->format('d M Y H:i');
    }

    private function formatHistoryTime($history): string
    {
        if (!$history || empty($history->time)) {
            return '-';
        }

        $timezone = optional(optional(optional(optional($history->display)->workstation)->workgroup)->facility)->timezone
            ?: config('app.timezone', 'UTC');

        return Carbon::createFromTimestampUTC((int) $history->time)->setTimezone($timezone)->format('d M Y H:i');
    }

    private function formatReportDate($value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('d M Y H:i');
        }

        $value = trim((string) $value);
        if ($value === '' || $value === '0') {
            return '-';
        }

        if (is_numeric($value)) {
            return Carbon::createFromTimestamp((int) $value)->format('d M Y H:i');
        }

        try {
            return Carbon::parse($value)->format('d M Y H:i');
        } catch (\Throwable $e) {
            return $value;
        }
    }

    private function plainValue($value): string
    {
        if (is_array($value)) {
            $value = $value['value'] ?? '';
        }

        return trim(strip_tags((string) $value)) ?: '-';
    }

    private function embedPublicImage(string $relativePath): string
    {
        $fullPath = public_path($relativePath);
        if (!is_file($fullPath)) {
            return '';
        }

        $extension = strtolower((string) pathinfo($fullPath, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'svg' => 'image/svg+xml',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/png',
        };

        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($fullPath));
    }
}
