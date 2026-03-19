<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistoriesController extends Controller
{
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
        $graph = $request->input('graph');
        if ($graph) {
            $graph = json_decode($graph, true);
        } else {
            $graph = [];
        }
        //$item = \App\Models\History::with('display.workstation.workgroup')->find($id);
        $item = \App\Models\History::find($id);
        $item->load('display.workstation.workgroup');
        
        //$version = File::get(base_path().'/version.txt');

        //$pdf = \PDF::loadView('histories.pdf',  compact('item', 'graph', 'version'));
        
        //$item = \App\Models\History::find($id);
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

        return view('histories.print_preview', [
            'title' => 'History Print Preview',
            'historyReport' => $this->buildHistoryPayload($history),
        ]);
    }

    private function resolveAuthorizedHistory(Request $request, $id)
    {
        $userId = $request->session()->get('id');
        $user = \App\Models\User::find($userId);
        $userRole = $request->session()->get('role');

        $history = \App\Models\History::with('display.workstation.workgroup.facility')->findOrFail($id);
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

        $header = collect($history->header)->filter(function ($value) {
            return trim((string) $value) !== '';
        })->map(function ($value, $key) {
            return [
                'label' => $key === 'Serial Number' ? 'Display Serial Number' : $key,
                'value' => $value ?: '-',
            ];
        })->values();

        if ($header->isEmpty()) {
            $header = collect([
                ['label' => 'Report Name', 'value' => $history->name ?: '-'],
                ['label' => 'Performed Date', 'value' => $history->getTimeDisplay()],
                ['label' => 'Facility', 'value' => optional($facility)->name ?: '-'],
                ['label' => 'Workgroup', 'value' => optional($workgroup)->name ?: '-'],
                ['label' => 'Workstation', 'value' => optional($workstation)->name ?: '-'],
                ['label' => 'Display', 'value' => optional($display)->treetext ?: '-'],
            ]);
        }

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

        return [
            'id' => $history->id,
            'name' => $history->name,
            'performedAt' => $history->getTimeDisplay(),
            'resultLabel' => $history->result_desc ?? 'Unknown',
            'resultTone' => match ((int) $history->result) {
                2 => 'success',
                3 => 'danger',
                4, 5 => 'warning',
                default => 'neutral',
            },
            'display' => [
                'facility' => optional($facility)->name ?? '-',
                'workgroup' => optional($workgroup)->name ?? '-',
                'workstation' => optional($workstation)->name ?? '-',
                'display' => optional($display)->treetext ?? '-',
            ],
            'header' => $header->values(),
            'sections' => $sections,
            'printUrl' => url('histories/' . $history->id . '/preview'),
        ];
    }
}
