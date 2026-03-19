<x-history-graphs variant="classic">
    @foreach ($graphs as $j => $graph)
        @php
            $ymax = isset($graph['ymax']) ? $graph['ymax'] : 0;
            $ymin = isset($graph['ymin']) ? $graph['ymin'] : 0;
        @endphp

        @if ($graph['type'] == 'spect')
            <img src="/graph/spect/{{ $history_id }}/{{ $step_id }}/{{ $j }}" />
        @else
            <canvas id="graph_{{ $i }}_{{ $j }}"></canvas>

            @if ($graph['lines'])
                @section('content-script')
                    <script>
                        var ctx_{{ $i }}_{{ $j }} = $("#graph_{{ $i }}_{{ $j }}");
                        var type = '{{ $graph['type'] == 'chhist' ? 'horizontalBar' : ($graph['type'] == 'hist' ? 'bar' : 'line') }}';
                        @if ($graph['type'] == 'chhist')
                        var legend = false;
                        var maintainAspectRatio = false;
                        var data = {
                            labels: [
                                @foreach ($graph['lines'] as $line)
                                    '{{ App\Http\Controllers\HistoriesController::hex2rgb($line['color']) }}',
                                @endforeach
                            ],
                            datasets: [{
                                borderColor: '#333',
                                borderWidth: 2,
                                backgroundColor: [
                                    @foreach ($graph['lines'] as $line)
                                        '{{ $line['color'] }}',
                                    @endforeach
                                ],
                                data: [
                                    @foreach ($graph['lines'] as $line)
                                        @foreach ($line['points'] as $p)
                                            {{ $p['y'] }},
                                        @endforeach
                                    @endforeach
                                ]
                            }]
                        };
                        @else
                        var legend = true;
                        var maintainAspectRatio = true;
                        var data = {
                            @if ($graph['type'] == 'hist')
                            labels: [0,15,30,45,60,75,90,105,120,135,150,165,180,195,210,225,240,255],
                            @endif
                            datasets: [
                                @foreach ($graph['lines'] as $line)
                                {
                                    @php
                                        $line['type'] = isset($line['type']) ? $line['type'] : 'line';
                                    @endphp
                                    label: '{{ isset($line['name']) ? $line['name'] : '' }}',
                                    borderColor: '{{ isset($line['color']) ? $line['color'] : '' }}',
                                    backgroundColor: '{{ isset($line['color']) ? $line['color'] : '' }}',
                                    borderWidth: 2,
                                    pointRadius: {{ $line['type'] == 'dot' ? 3 : 0 }},
                                    fill: false,
                                    showLine: {{ $line['type'] == 'line' ? 'true' : 'false' }},
                                    data: [
                                        @if ($graph['type'] == 'hist')
                                            @php
                                                $newp = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
                                                foreach ($line['points'] as $p) {
                                                    $newp[$p['x'] / 15] = $p['y'];
                                                }
                                            @endphp

                                            @foreach ($newp as $p)
                                                {{ $p }},
                                            @endforeach
                                        @else
                                            @foreach ($line['points'] as $p)
                                            {
                                                @php
                                                    if ($p['y'] > $ymax) $ymax = $p['y'];
                                                    if ($p['y'] < $ymin) $ymin = $p['y'];
                                                @endphp
                                                x: {{ $p['x'] }},
                                                y: {{ $p['y'] }}
                                            },
                                            @endforeach
                                        @endif
                                    ],
                                },
                                @endforeach
                            ]
                        };
                        @endif
                        var myChart = new Chart(ctx_{{ $i }}_{{ $j }}, {
                            type: type,
                            data: data,
                            options: {
                                maintainAspectRatio: maintainAspectRatio,
                                responsive: true,
                                @if ($graph['type'] == 'line')
                                scales: {
                                    xAxes: [{
                                        type: 'linear',
                                        position: 'bottom',
                                        @if ($graph['name'] == 'luminance')
                                            ticks: {min: 0, max: 255, minRotation: 45},
                                        @endif
                                    }],
                                    yAxes: [{
                                        ticks: {
                                            {{ ($ymin) ? ('min: '.$ymin.',') : '' }}
                                            {{ ($ymax > 0) ? ('max: '.$ymax) : '' }}
                                        }
                                    }]
                                },
                                @endif
                                legend: {
                                    display: legend,
                                    position: 'bottom',
                                },
                                @if (isset($graph['horizontals']))
                                annotation: {
                                    annotations: [
                                        @foreach ($graph['horizontals'] as $h)
                                        {
                                            type: 'line',
                                            mode: 'horizontal',
                                            scaleID: 'y-axis-0',
                                            value: {{ $h['level'] }},
                                            borderColor: '{{ $h['color'] }}',
                                            borderWidth: 2,
                                            label: {
                                                enabled: false,
                                                content: 'Level: {{ $h['level'] }}'
                                            }
                                        },
                                        @endforeach
                                    ]
                                },
                                @endif
                            },
                        });
                        @if ($graph['type'] == 'chhist')
                        ctx_{{ $i }}_{{ $j }}.height({{ count($graph['lines']) * 20 }});
                        @endif
                    </script>
                @append
            @endif
        @endif
    @endforeach
</x-history-graphs>
