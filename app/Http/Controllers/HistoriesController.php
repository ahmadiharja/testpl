<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class HistoriesController extends Controller
{
    private function resolveHistoryTaskTypeKey(\App\Models\History $history): ?string
    {
        $name = mb_strtolower(trim((string) ($history->name ?? '')));
        $regulation = mb_strtolower(trim((string) ($history->regulation ?? '')));

        $candidates = array_values(array_filter([$name, $regulation]));
        foreach ($candidates as $value) {
            if (str_contains($value, 'display test pattern')) return 'dtp';
            if (str_contains($value, 'mqsa')) return 'mmi';
            if (str_contains($value, 'everlight')) return 'ela';
            if (str_contains($value, 'full black')) return 'fbs';
            if (str_contains($value, 'smpte us rad')) return 'sur';
            if ($value === 'smpte') return 'dtp';
            if (str_contains($value, 'calibration conformance') || str_contains($value, 'display conformance')) return 'con';
            if (str_contains($value, 'luminance calibration')) return 'awl';
            if (str_contains($value, 'luminance conformance')) return 'vwl';
            if (str_contains($value, 'create icc')) return 'icc';
            if (str_contains($value, 'calibration')) return 'cal';
        }

        return null;
    }

    public function histories(Request $request)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);
        $role=$request->session()->get('role');
        
        $cacheKey = '';
        if ($role!='super') { // load current facility only
            $facilities = array($user->facility);
            //var_dump($facilities); exit();

        } else { // load all facilities
            $facilities = \App\Models\Facility::all();
        }
        
        return view('histories.index', ['title' =>'Histories & Reports', 'facilities'=>$facilities]);
    }
    
    public function view_histories(Request $request, $id)
    {
        $item = $this->resolveAuthorizedHistory($request, $id);
        $graph = $request->input('graph');
        if ($graph) {
            $graph = json_decode($graph, true);
        } else {
            $graph = [];
        }
        return view('histories.view', ['title'=>'History Information'])->with('item', $item);
    }

    public function api_history_modal(Request $request, $id)
    {
        $history = $this->resolveAuthorizedHistory($request, $id);
        return response()->json($this->buildHistoryPayload($history));
    }

    public function print_preview(Request $request, $id)
    {
        $history = $this->resolveAuthorizedHistory($request, $id);
        $payload = $this->buildHistoryPayload($history);
        $tempDir = storage_path('app/mpdf-temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $html = view('histories.print_preview', [
            'title' => 'History Print Preview',
            'historyReport' => $payload,
            'logoPerfectLum' => $this->embedPublicImage('assets/images/perfectlum-logo.png'),
            'logoQubyx' => $this->embedPublicImage('assets/images/qubyx-black.png'),
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 14,
            'margin_right' => 14,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 0,
            'margin_footer' => 8,
            'default_font' => 'dejavusans',
            'tempDir' => $tempDir,
        ]);
        $mpdf->SetTitle($payload['name'] . ' Report');
        $mpdf->SetAuthor('PerfectLum');
        $mpdf->SetDisplayMode('fullwidth');
        $mpdf->SetAutoPageBreak(true, 16);
        $mpdf->showImageErrors = false;
        $mpdf->SetHTMLFooter('
            <div style="border-top:1px solid #d7dee8; padding-top:6px; font-size:9px; color:#667085; letter-spacing:0.12em; text-transform:uppercase;">
                <table width="100%" style="border-collapse:collapse;">
                    <tr>
                        <td style="font-size:9px; color:#667085;">' . e($payload['name']) . ' · qubyx.com</td>
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

            $filename = preg_replace('/[^A-Za-z0-9\\-_]+/', '-', $payload['name'] ?: 'report') . '.pdf';
            return response($mpdf->Output($filename, Destination::STRING_RETURN), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            ]);
        } finally {
            error_reporting($previousErrorReporting);
            ini_set('display_errors', $previousDisplayErrors === false ? '0' : (string) $previousDisplayErrors);
        }
    }

    private function resolveAuthorizedHistory(Request $request, $id)
    {
        $userId = $request->session()->get('id');
        $user = \App\Models\User::find($userId);
        $userRole = $request->session()->get('role');

        $history = \App\Models\History::with('display.workstation.workgroup.facility', 'syncResolution.display')->findOrFail($id);
        $facility = optional(optional(optional($history->display)->workstation)->workgroup)->facility;

        if ($userRole !== 'super' && (!$user || !$facility || (int) $facility->id !== (int) $user->facility_id)) {
            abort(404);
        }

        return $history;
    }

    private function buildHistoryPayload(\App\Models\History $history)
    {
        $display = $history->display;
        $workstation = optional($display)->workstation;
        $workgroup = optional($workstation)->workgroup;
        $facility = optional($workgroup)->facility;
        $syncResolution = $history->syncResolution;
        $taskTypeKey = $this->resolveHistoryTaskTypeKey($history);
        $timezone = optional($facility)->timezone ?: config('app.timezone', 'UTC');
        $performedAt = \Carbon\Carbon::createFromTimestampUTC((int) $history->time)
            ->setTimezone($timezone);
        $printedAt = now()->setTimezone($timezone);

        $displayPreference = function (string $name, string $fallback = '-') use ($display) {
            $value = trim((string) optional($display)->preference($name));
            return $value !== '' ? $value : $fallback;
        };

        $workstationPreference = function (string $name, string $fallback = '-') use ($workstation) {
            $value = trim((string) optional($workstation)->preference($name));
            return $value !== '' ? $value : $fallback;
        };

        $softwareVersion = trim((string) optional($workstation)->client_version);
        if ($softwareVersion === '') {
            $appName = trim((string) $workstationPreference('appname', 'PerfectLum'));
            $appVersion = trim((string) $workstationPreference('appversion', ''));
            $softwareVersion = trim($appName . ($appVersion !== '' ? ' ' . $appVersion : ''));
        }

        $displayCategory = $displayPreference('display_type', '-');
        if ($displayCategory === '-') {
            $displayCategory = $displayPreference('TypeOfDisplay', '-');
        }

        $videoAdapter = collect([
            $displayPreference('VideoAdapter', ''),
            $displayPreference('VideoAdapterName', ''),
            $displayPreference('GraphicsAdapter', ''),
            $displayPreference('Graphics', ''),
            $displayPreference('GPU', ''),
            $workstationPreference('VideoAdapter', ''),
            $workstationPreference('VideoAdapterName', ''),
            $workstationPreference('GraphicsAdapter', ''),
            $workstationPreference('Graphics', ''),
            $workstationPreference('GPU', ''),
            $workstationPreference('AdapterName', ''),
        ])->first(function ($value) {
            return trim((string) $value) !== '';
        }) ?: '-';

        $reportFactsLeft = collect([
            ['label' => 'Facility', 'value' => optional($facility)->name ?: '-'],
            ['label' => 'Department', 'value' => '-'],
            ['label' => 'Room', 'value' => '-'],
            ['label' => 'Workstation Name', 'value' => optional($workstation)->name ?: '-'],
            ['label' => 'Responsible Person Name', 'value' => '-'],
            ['label' => 'Address', 'value' => '-'],
            ['label' => 'Email', 'value' => $workstationPreference('ActivationEmailAddress', '-')],
            ['label' => 'Phone Number', 'value' => '-'],
        ])->values();

        $reportFactsRight = collect([
            ['label' => 'Manufacturer', 'value' => $displayPreference('Manufacturer', optional($display)->manufacturer ?: '-')],
            ['label' => 'Model', 'value' => $displayPreference('Model', optional($display)->model ?: '-')],
            ['label' => 'Display Technology', 'value' => $displayPreference('DisplayTechnology', '-')],
            ['label' => 'Type Of Display', 'value' => $displayPreference('TypeOfDisplay', optional($display)->type_of_display ?: '-')],
            ['label' => 'Serial Number', 'value' => $displayPreference('SerialNumber', optional($display)->serial ?: '-')],
            ['label' => 'Display Category', 'value' => $displayCategory],
            ['label' => 'Display Inventory Number', 'value' => $displayPreference('InventoryNumber', $displayPreference('Inventarnummer', '-'))],
            ['label' => 'Video Adapter', 'value' => $videoAdapter],
        ])->values();

        $reportFactsFooter = collect([
            ['label' => 'Date of test', 'value' => $performedAt->format('d/m/Y H:i')],
            ['label' => 'Date of print', 'value' => $printedAt->format('d/m/Y H:i')],
            ['label' => 'Test Image Source', 'value' => 'PerfectLum'],
            ['label' => 'Software Version', 'value' => $softwareVersion !== '' ? $softwareVersion : 'PerfectLum'],
        ])->values();

        $header = collect($history->header)->filter(function ($value) {
            return trim((string) $value) !== '';
        })->map(function ($value, $key) {
            return [
                'label' => $key === 'Serial Number' ? 'Display Serial Number' : $key,
                'value' => $value ?: '-',
            ];
        })->values();

        $sections = collect($history->steps)->map(function ($step, $stepIndex) use ($history) {
            $scores = collect($step['scores'] ?? [])->map(function ($score) {
                $answer = $score['answer'] ?? null;

                return [
                    'name' => $score['name'] ?? '-',
                    'limit' => $score['limit'] ?? '-',
                    'measured' => $score['measured'] ?? '-',
                    'statusLabel' => $answer === 1 ? 'OK' : ($answer === 0 ? 'Not OK' : '-'),
                    'statusTone' => $answer === 1 ? 'success' : ($answer === 0 ? 'danger' : 'neutral'),
                ];
            })->values();

            $questions = collect($step['questions'] ?? [])->map(function ($question) {
                $answer = (string) ($question['answer'] ?? '');
                $reverse = strtolower((string) ($question['reverse'] ?? ''));
                $tone = 'neutral';

                if ($answer !== '') {
                    $answerLower = strtolower($answer);
                    $tone = (($reverse === 'yes' && $answerLower === 'no') || ($reverse !== 'yes' && $answerLower === 'yes'))
                        ? 'success'
                        : 'danger';
                }

                return [
                    'text' => $question['text'] ?? '-',
                    'answer' => $answer ?: '-',
                    'tone' => $tone,
                ];
            })->values();

            $graphs = collect($step['graphs'] ?? [])->map(function ($graph, $graphIndex) use ($history, $stepIndex) {
                $type = $graph['type'] ?? 'image';
                $lines = collect($graph['lines'] ?? [])->map(function ($line) {
                    return [
                        'name' => $line['name'] ?? '',
                        'color' => $line['color'] ?? '#0ea5e9',
                        'points' => collect($line['points'] ?? [])->map(function ($point) {
                            return [
                                'x' => (float) ($point['x'] ?? 0),
                                'y' => (float) ($point['y'] ?? 0),
                            ];
                        })->values(),
                    ];
                })->values();

                return [
                    'name' => $graph['name'] ?? ('Graph ' . ($graphIndex + 1)),
                    'type' => $type,
                    'url' => $type === 'spect'
                        ? url("/graph/spect/{$history->id}/{$stepIndex}/{$graphIndex}")
                        : null,
                    'chart' => in_array($type, ['line', 'hist', 'chhist'], true) && $lines->isNotEmpty(),
                    'lines' => $lines,
                    'xMin' => $lines->flatMap(fn($line) => $line['points'])->min('x') ?? 0,
                    'xMax' => $lines->flatMap(fn($line) => $line['points'])->max('x') ?? 100,
                    'yMin' => isset($graph['ymin']) ? (float) $graph['ymin'] : ($lines->flatMap(fn($line) => $line['points'])->min('y') ?? 0),
                    'yMax' => isset($graph['ymax']) ? (float) $graph['ymax'] : ($lines->flatMap(fn($line) => $line['points'])->max('y') ?? 100),
                ];
            })->values();

            $comment = trim(strip_tags((string) ($step['comment'] ?? '')));

            if ($scores->isEmpty() && $questions->isEmpty() && $graphs->isEmpty() && $comment === '') {
                return null;
            }

            return [
                'name' => $step['name'] ?? ('Step ' . ($stepIndex + 1)),
                'scores' => $scores,
                'questions' => $questions,
                'comment' => $comment,
                'graphs' => $graphs,
            ];
        })->filter()->values();

        $summaryRows = $sections->flatMap(function ($section) {
            return collect($section['questions'] ?? [])->map(function ($question) use ($section) {
                return [
                    'section' => $section['name'] ?? '-',
                    'question' => $question['text'] ?? '-',
                    'answer' => $question['answer'] ?? '-',
                    'tone' => $question['tone'] ?? 'neutral',
                ];
            });
        })->values();

        return [
            'id' => $history->id,
            'name' => $history->name,
            'performedAtTs' => (int) $history->time,
            'performedAtIso' => \Carbon\Carbon::createFromTimestampUTC((int) $history->time)->format('Y-m-d\TH:i:s'),
            'performedAt' => $performedAt->format('d/m/Y H:i'),
            'resultLabel' => $history->result_desc ?? 'Unknown',
            'resultTone' => match ((int) $history->result) {
                2 => 'success',
                3 => 'danger',
                4, 5 => 'warning',
                default => 'neutral',
            },
            'display' => [
                'id' => optional($display)->id,
                'facility' => optional($facility)->name ?? '-',
                'workgroup' => optional($workgroup)->name ?? '-',
                'workstation' => optional($workstation)->name ?? '-',
                'display' => optional($display)->treetext ?? '-',
            ],
            'reschedule' => [
                'taskTypeKey' => $taskTypeKey,
                'displayId' => optional($display)->id,
                'facilityId' => optional($facility)->id,
                'workgroupId' => optional($workgroup)->id,
                'workstationId' => optional($workstation)->id,
                'startDate' => $performedAt->format('Y-m-d'),
                'startTime' => $performedAt->format('H:i'),
                'dayOfMonth' => max(1, (int) $performedAt->format('j')),
                'monthNumber' => max(1, (int) $performedAt->format('n')),
                'dayOfWeek' => max(1, (int) $performedAt->isoWeekday()),
            ],
            'header' => $header->values(),
            'sections' => $sections,
            'summaryRows' => $summaryRows,
            'reportFactsLeft' => $reportFactsLeft,
            'reportFactsRight' => $reportFactsRight,
            'reportFactsFooter' => $reportFactsFooter,
            'printedAt' => $printedAt->format('d/m/Y H:i'),
            'softwareVersion' => $softwareVersion !== '' ? $softwareVersion : 'PerfectLum',
            'printUrl' => url('histories/' . $history->id . '/preview'),
            'syncResolution' => $syncResolution ? [
                'method' => $syncResolution->method,
                'confidence' => $syncResolution->confidence,
                'notes' => $syncResolution->notes,
                'requestedClientId' => $syncResolution->requested_client_id,
                'resolvedDisplay' => optional($syncResolution->display)->treetext ?: optional($display)->treetext ?: '-',
                'context' => $syncResolution->context ?? [],
            ] : null,
        ];
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
