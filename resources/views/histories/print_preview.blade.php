<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $historyReport['name'] }} - Print Preview</title>
    <style>
        :root {
            --bg: #f3f7fb;
            --surface: #ffffff;
            --surface-soft: #f8fbff;
            --border: #dbe5f0;
            --text: #14213d;
            --muted: #7b8cab;
            --accent: #147df5;
            --accent-soft: #e9f3ff;
        }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; margin: 0; background: linear-gradient(180deg, #eef5fb 0%, #f8fbff 100%); color: var(--text); }
        .shell { max-width: 980px; margin: 0 auto; padding: 28px 22px 44px; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 18px; margin-bottom: 22px; padding: 20px 24px; border: 1px solid var(--border); border-radius: 24px; background: linear-gradient(135deg, #ffffff 0%, #f5faff 100%); box-shadow: 0 18px 50px -30px rgba(20, 33, 61, 0.45); }
        .button { border: 1px solid #bfcee2; background: white; color: var(--text); padding: 10px 16px; border-radius: 999px; font-weight: 700; cursor: pointer; text-decoration: none; }
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: 22px; padding: 20px; margin-bottom: 16px; box-shadow: 0 16px 42px -34px rgba(20, 33, 61, 0.5); }
        .label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.16em; color: var(--muted); }
        .title { font-size: 30px; line-height: 1.15; font-weight: 800; margin: 8px 0 0; max-width: 720px; }
        .subtitle { margin-top: 8px; color: var(--muted); font-size: 14px; }
        .meta { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
        .meta-item { border: 1px solid var(--border); background: var(--surface-soft); border-radius: 16px; padding: 14px; }
        .meta-item strong { display: block; margin-top: 8px; font-size: 14px; }
        .badge { display: inline-block; padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .success { background: #dcfce7; color: #166534; }
        .danger { background: #fee2e2; color: #991b1b; }
        .warning { background: #fef3c7; color: #92400e; }
        .neutral { background: #e2e8f0; color: #475569; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; overflow: hidden; border-radius: 16px; }
        th, td { border-bottom: 1px solid var(--border); padding: 12px; text-align: left; font-size: 14px; vertical-align: top; }
        th { font-size: 11px; text-transform: uppercase; letter-spacing: 0.12em; color: var(--muted); background: #f8fbff; }
        .question { display: flex; justify-content: space-between; gap: 16px; border: 1px solid var(--border); background: var(--surface-soft); border-radius: 14px; padding: 14px; margin-top: 12px; }
        .comment { border: 1px solid var(--border); background: var(--surface-soft); border-radius: 14px; padding: 14px; margin-top: 16px; }
        .graphs { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-top: 16px; }
        .graph { border: 1px solid var(--border); border-radius: 18px; overflow: hidden; background: var(--surface-soft); }
        .graph-title { padding: 12px 14px; border-bottom: 1px solid var(--border); font-weight: 700; }
        .graph img { display: block; width: 100%; background: white; }
        .graph-canvas { background: white; padding: 14px; }
        .graph-canvas svg { display: block; width: 100%; height: 220px; overflow: visible; }
        .step-title { font-size: 22px; font-weight: 800; margin-top: 10px; }
        .hero-row { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; }
        @media print {
            body { background: white; }
            .toolbar { display: none; }
            .shell { max-width: none; padding: 0; }
            .card { break-inside: avoid; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="toolbar">
            <div>
                <div class="label">Print Preview</div>
                <div class="title">{{ $historyReport['name'] }}</div>
                <div class="subtitle">Formatted report for browser preview and direct printing.</div>
            </div>
            <button class="button" onclick="window.print()">Print</button>
        </div>

        <div class="card">
            <div class="hero-row">
                <div>
                    <div class="label">Performed At</div>
                    <div style="font-size:20px;font-weight:800;margin-top:8px;">{{ $historyReport['performedAt'] }}</div>
                </div>
                <span class="badge {{ $historyReport['resultTone'] }}">{{ $historyReport['resultLabel'] }}</span>
            </div>
            <div class="meta">
                <div class="meta-item"><span class="label">Facility</span><strong>{{ $historyReport['display']['facility'] }}</strong></div>
                <div class="meta-item"><span class="label">Workgroup</span><strong>{{ $historyReport['display']['workgroup'] }}</strong></div>
                <div class="meta-item"><span class="label">Workstation</span><strong>{{ $historyReport['display']['workstation'] }}</strong></div>
            </div>
        </div>

        <div class="card">
            <div class="label">Header Details</div>
            <div class="meta">
                @foreach ($historyReport['header'] as $item)
                    <div class="meta-item">
                        <span class="label">{{ $item['label'] }}</span>
                        <strong>{{ $item['value'] }}</strong>
                    </div>
                @endforeach
            </div>
        </div>

        @foreach ($historyReport['sections'] as $section)
            <div class="card">
                <div class="label">Step</div>
                <div class="step-title">{{ $section['name'] }}</div>

                @if (count($section['scores']))
                    <x-history-scores variant="print">
                        @foreach ($section['scores'] as $score)
                            <tr>
                                <td>{!! $score['name'] !!}</td>
                                <td>{!! $score['limit'] !!}</td>
                                <td>{!! $score['measured'] !!}</td>
                                <td><span class="badge {{ $score['statusTone'] }}">{{ $score['statusLabel'] }}</span></td>
                            </tr>
                        @endforeach
                    </x-history-scores>
                @endif

                <x-history-questions variant="print">
                    @foreach ($section['questions'] as $question)
                        <div class="question">
                            <div>{!! $question['text'] !!}</div>
                            <span class="badge {{ $question['tone'] }}">{{ $question['answer'] }}</span>
                        </div>
                    @endforeach
                </x-history-questions>

                @if ($section['comment'])
                    <div class="comment">
                        <div class="label">Comment</div>
                        <div style="margin-top:10px;">{{ $section['comment'] }}</div>
                    </div>
                @endif

                @if (count($section['graphs']))
                    <x-history-graphs variant="print">
                        @foreach ($section['graphs'] as $graph)
                            <div class="graph">
                                <div class="graph-title">{{ $graph['name'] }}</div>
                                @if (!empty($graph['chart']))
                                    @php
                                        $xMin = (float) ($graph['xMin'] ?? 0);
                                        $xMax = (float) ($graph['xMax'] ?? 100);
                                        $yMin = (float) ($graph['yMin'] ?? 0);
                                        $yMax = (float) ($graph['yMax'] ?? 100);
                                        $xRange = ($xMax - $xMin) ?: 1;
                                        $yRange = ($yMax - $yMin) ?: 1;
                                    @endphp
                                    <div class="graph-canvas">
                                        <svg viewBox="0 0 100 56" preserveAspectRatio="none">
                                            @foreach ($graph['lines'] as $line)
                                                @php
                                                    $polyline = collect($line['points'] ?? [])->map(function ($point) use ($xMin, $xRange, $yMin, $yRange) {
                                                        $rawX = (float) ($point['x'] ?? 0);
                                                        $rawY = (float) ($point['y'] ?? 0);
                                                        $x = (($rawX - $xMin) / $xRange) * 100;
                                                        $y = 52 - ((($rawY - $yMin) / $yRange) * 46);
                                                        $x = max(0, min(100, $x));
                                                        $y = max(4, min(52, $y));
                                                        return round($x, 3) . ',' . round($y, 3);
                                                    })->implode(' ');
                                                @endphp
                                                @if ($polyline !== '')
                                                    <polyline
                                                        fill="none"
                                                        stroke="{{ $line['color'] ?? '#147df5' }}"
                                                        stroke-width="1.35"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        points="{{ $polyline }}"
                                                    ></polyline>
                                                @endif
                                            @endforeach
                                        </svg>
                                    </div>
                                @elseif (!empty($graph['url']))
                                    <img src="{{ $graph['url'] }}" alt="{{ $graph['name'] }}">
                                @endif
                            </div>
                        @endforeach
                    </x-history-graphs>
                @endif
            </div>
        @endforeach
    </div>
</body>
</html>
