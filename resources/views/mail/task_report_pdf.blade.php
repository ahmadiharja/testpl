<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 22px;
            margin-bottom: 5px;
        }
        .header div {
            margin-bottom: 3px;
            color: #555;
        }
        .result-ok {
            color: #27AE60;
            font-weight: bold;
            font-size: 16px;
        }
        .result-failed {
            color: #EB5757;
            font-weight: bold;
            font-size: 16px;
        }
        .step-header {
            font-size: 16px;
            color: #2CA8FF;
            font-weight: 700;
            margin-top: 25px;
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table th {
            background-color: #2CA8FF;
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
        }
        table td {
            padding: 5px 8px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-success { color: #27AE60; }
        .text-danger { color: #EB5757; }
        .comment-section {
            margin: 8px 0;
            padding: 8px;
            background: #f5f5f5;
            border-left: 3px solid #2CA8FF;
        }
        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 5px 0;
        }
        .footer {
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $item->name }}</h1>
        @if($item->display)
        <div>Display: {{ $item->display->manufacturer }} {{ $item->display->model }} ({{ $item->display->serial }})</div>
        @if($item->display->workstation)
        <div>Workstation: {{ $item->display->workstation->name }}
            @if($item->display->workstation->workgroup)
            | Workgroup: {{ $item->display->workstation->workgroup->name }}
                @if($item->display->workstation->workgroup->facility)
                | Facility: {{ $item->display->workstation->workgroup->facility->name }}
                @endif
            @endif
        </div>
        @endif
        @endif
        @if($item->getHeader('Sensor Model') !== '')
        <div>Sensor: {{ $item->getHeader('Sensor Manufacturer') }}, {{ $item->getHeader('Sensor Model') }} | Serial: {{ $item->getHeader('Sensor Serial') }}</div>
        @endif
        <div>Performed Date: {{ $item->getTimeDisplay() }}</div>
        <div>
            Result: 
            @if($item->result == 2)
                <span class="result-ok">OK</span>
            @elseif($item->result == 3)
                <span class="result-failed">Failed</span>
            @else
                {{ $item->status_text }}
            @endif
        </div>
    </div>

    <div class="tab-content">
        @foreach ($item->steps as $i => $step)
        <div>
            <div class="step-header">{{ ucfirst($step['name']) }}</div>
            <hr />

            @if (isset($step['scores']))
            <table>
                <thead>
                    <tr>
                        <th>Items</th>
                        <th>Target Settings</th>
                        <th>Results</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($step['scores'] as $score)
                    <tr>
                        <td>{!! $score['name'] !!}</td>
                        <td>{!! $score['limit'] ? $score['limit'] : '-' !!}</td>
                        <td>{!! $score['measured'] !!}</td>
                        <td>{!! $score['answer'] == 0 ? '<span class="text-danger"><b>Not OK</b></span>' : ($score['answer'] == 1 ? '<span class="text-success"><b>OK</b></span>' : '-') !!}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            @if (isset($step['questions']))
            <table>
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Answer</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($step['questions'] as $question)
                    <tr>
                        <td>{!! $question['text'] !!}</td>
                        @if (isset($question['reverse']) AND (strtolower($question['answer']) == ($question['reverse'] == 'yes' ? 'no' : 'yes')))
                        <td><span class="text-success"><b>{!! $question['answer'] !!}</b></span></td>
                        @else
                        <td><span class="text-danger"><b>{!! $question['answer'] !!}</b></span></td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            @if (isset($step['comment']))
            <div class="comment-section"><b>Comment:</b> {!! $step['comment'] !!}</div>
            @endif
            
            @if (isset($step['graphs']))
            <!-- Graphs -->
            @foreach ($step['graphs'] as $k => $graph)
                @if ($graph['type'] == 'spect')
                    <div style="text-align: center; margin-top: 15px;">
                        <img src="{{ url('/graph/spect/' . $item->id . '/' . $i . '/' . $k) }}" width="80%" />
                    </div>
                @else
                    @php
                        $ymax = isset($graph['ymax'])?$graph['ymax']:0;
                        $ymin = isset($graph['ymin'])?$graph['ymin']:0;
                        $type = $graph['type'] == 'chhist' ? 'horizontalBar': ($graph['type'] == 'hist' ? 'bar' : 'line');
                        
                        $chartData = [
                            'type' => $type,
                            'data' => [
                                'labels' => [],
                                'datasets' => []
                            ],
                            'options' => [
                                'maintainAspectRatio' => false,
                                'legend' => ['display' => $graph['type'] != 'chhist', 'position' => 'bottom'],
                            ]
                        ];
                        
                        if ($graph['type'] == 'chhist') {
                            $labels = [];
                            $bgColors = [];
                            $dataPoints = [];
                            foreach ($graph['lines'] as $line) {
                                $labels[] = \App\Http\Controllers\HistoriesController::hex2rgb($line['color']);
                                $bgColors[] = $line['color'];
                                foreach ($line['points'] as $p) {
                                    $dataPoints[] = $p['y'];
                                }
                            }
                            $chartData['data'] = [
                                'labels' => $labels,
                                'datasets' => [[
                                    'borderColor' => '#333',
                                    'borderWidth' => 2,
                                    'backgroundColor' => $bgColors,
                                    'data' => $dataPoints
                                ]]
                            ];
                        } else {
                            if ($graph['type'] == 'hist') {
                                $chartData['data']['labels'] = [0,15,30,45,60,75,90,105,120,135,150,165,180,195,210,225,240,255];
                            }
                            $datasets = [];
                            foreach ($graph['lines'] as $line) {
                                $lineType = isset($line['type']) ? $line['type'] : 'line';
                                $dataset = [
                                    'label' => isset($line['name']) ? $line['name'] : '',
                                    'borderColor' => isset($line['color']) ? $line['color'] : '',
                                    'backgroundColor' => isset($line['color']) ? $line['color'] : '',
                                    'borderWidth' => 2,
                                    'pointRadius' => ($lineType == 'dot') ? 3 : 0,
                                    'fill' => false,
                                    'showLine' => ($lineType == 'line')
                                ];
                                
                                if ($graph['type'] == 'hist') {
                                    $newp = array_fill(0, 18, 0);
                                    foreach ($line['points'] as $p) {
                                        $newp[intval($p['x'] / 15)] = $p['y'];
                                    }
                                    $dataset['data'] = array_values($newp);
                                } else {
                                    $points = [];
                                    foreach ($line['points'] as $p) {
                                        if ($p['y'] > $ymax) $ymax = $p['y'];
                                        if ($p['y'] < $ymin) $ymin = $p['y'];
                                        $points[] = ['x' => $p['x'], 'y' => $p['y']];
                                    }
                                    $dataset['data'] = $points;
                                }
                                $datasets[] = $dataset;
                            }
                            $chartData['data']['datasets'] = $datasets;
                            
                            if ($graph['type'] == 'line') {
                                $chartData['options']['scales'] = [
                                    'xAxes' => [['type' => 'linear', 'position' => 'bottom']],
                                    'yAxes' => [[]]
                                ];
                                if (isset($graph['name']) && strtolower($graph['name']) == 'luminance') {
                                    $chartData['options']['scales']['xAxes'][0]['ticks'] = ['min' => 0, 'max' => 255, 'minRotation' => 45];
                                }
                                if ($ymin || $ymax > 0) {
                                    $ticks = [];
                                    if ($ymin) $ticks['min'] = $ymin;
                                    if ($ymax > 0) $ticks['max'] = $ymax;
                                    $chartData['options']['scales']['yAxes'][0]['ticks'] = $ticks;
                                }
                            }
                            if (isset($graph['horizontals'])) {
                                $annotations = [];
                                foreach ($graph['horizontals'] as $h) {
                                    $annotations[] = [
                                        'type' => 'line',
                                        'mode' => 'horizontal',
                                        'scaleID' => 'y-axis-0',
                                        'value' => $h['level'],
                                        'borderColor' => $h['color'],
                                        'borderWidth' => 2
                                    ];
                                }
                                $chartData['options']['annotation'] = ['annotations' => $annotations];
                            }
                        }
                        
                        $chartJson = json_encode($chartData);
                        $quickchartUrl = "https://quickchart.io/chart?w=600&h=300&c=" . urlencode($chartJson);
                        
                        // Fetch the image from QuickChart and convert it to Base64
                        $imgData = @file_get_contents($quickchartUrl);
                        if ($imgData) {
                            $base64 = 'data:image/png;base64,' . base64_encode($imgData);
                        } else {
                            $base64 = ''; // if fails
                        }
                    @endphp
                    @if ($base64)
                    <div style="text-align: center; margin-top: 15px;">
                        <img src="{{ $base64 }}" width="100%" />
                    </div>
                    @endif
                @endif
            @endforeach
            @endif
        </div>
        @endforeach
    </div>

    <div class="footer">PerfectLum version: {{ $version }}</div>
</body>

</html>
