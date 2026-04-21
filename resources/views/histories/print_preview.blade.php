<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $historyReport['name'] ?? 'History Report' }}</title>
</head>
<body style="margin:0; padding:0; font-family: dejavusans, sans-serif; color:#0f172a; font-size:10.5px; line-height:1.42; background:#ffffff;">
    @php
        $report = $historyReport ?? [];
        $leftFacts = collect($report['reportFactsLeft'] ?? []);
        $rightFacts = collect($report['reportFactsRight'] ?? []);
        $footerFacts = collect($report['reportFactsFooter'] ?? []);
        $summaryRows = collect($report['summaryRows'] ?? []);
        $sections = collect($report['sections'] ?? []);
        $resultLabel = strtoupper((string) ($report['resultLabel'] ?? 'UNKNOWN'));
        $resultTone = (string) ($report['resultTone'] ?? 'neutral');
        $resultColor = match ($resultTone) {
            'success' => '#15803d',
            'danger' => '#b91c1c',
            'warning' => '#b45309',
            default => '#334155',
        };
        $resultBg = match ($resultTone) {
            'success' => '#ecfdf3',
            'danger' => '#fef2f2',
            'warning' => '#fff7ed',
            default => '#f8fafc',
        };
    @endphp

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-bottom:14px;">
        <tr>
            <td width="56%" valign="middle">
                @if(!empty($logoPerfectLum))
                    <img src="{{ $logoPerfectLum }}" alt="PerfectLum" style="height:42px; display:block;">
                @else
                    <div style="font-size:24px; font-weight:700; color:#0f172a;">PerfectLum</div>
                @endif
            </td>
            <td width="44%" align="right" valign="middle">
                @if(!empty($logoQubyx))
                    <img src="{{ $logoQubyx }}" alt="QUBYX" style="height:28px; display:inline-block;">
                @else
                    <div style="font-size:20px; font-weight:700; color:#111827;">QUBYX</div>
                @endif
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-bottom:18px;">
        <tr>
            <td width="64%" valign="top" style="padding-right:18px;">
                <div style="font-size:8.5px; letter-spacing:0.24em; color:#64748b; text-transform:uppercase; margin-bottom:7px;">Display QA Report</div>
                <div style="font-size:20px; font-weight:700; line-height:1.18; color:#0f172a; margin-bottom:8px;">
                    {{ $report['name'] ?? 'History Report' }}
                </div>
                <div style="font-size:9.5px; color:#475569; max-width:340px; line-height:1.45;">
                    Structured verification summary prepared from the synchronized client report for printing and review.
                </div>
            </td>
            <td width="36%" valign="top" align="right">
                <table width="82%" align="right" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; background:{{ $resultBg }}; border:1px solid #dbe3ee;">
                    <tr>
                        <td style="padding:10px 12px 0 12px; font-size:8.5px; letter-spacing:0.18em; color:#64748b; text-transform:uppercase;" align="right">Result</td>
                    </tr>
                    <tr>
                        <td style="padding:2px 12px 6px 12px; font-size:16px; font-weight:700; color:{{ $resultColor }};" align="right">{{ $resultLabel }}</td>
                    </tr>
                    <tr>
                        <td style="padding:0 12px 12px 12px; font-size:9.5px; color:#475569;" align="right">
                            Test date: {{ $report['performedAt'] ?? '-' }}<br>
                            Printed: {{ $report['printedAt'] ?? '-' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-bottom:18px;">
        <tr>
            <td width="48%" valign="top">
                <div style="display:block; margin:0 0 10px 0;">
                    <span style="display:inline-block; padding:6px 10px; background:#eef4fb; border:1px solid #dbe7f3; font-size:8.5px; letter-spacing:0.24em; color:#334155; text-transform:uppercase; font-weight:700;">
                        Site Information
                    </span>
                </div>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                    @foreach($leftFacts as $fact)
                        <tr>
                            <td width="48%" style="padding:6px 10px; border-bottom:1px solid #e5e7eb; font-size:9.5px; color:#334155;">{{ $fact['label'] ?? '-' }}</td>
                            <td width="52%" style="padding:6px 10px; border-bottom:1px solid #e5e7eb; background:#f8fafc; font-size:9.5px; color:#0f172a;">{{ $fact['value'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
            <td width="4%"></td>
            <td width="48%" valign="top">
                <div style="display:block; margin:0 0 10px 0;">
                    <span style="display:inline-block; padding:6px 10px; background:#eef4fb; border:1px solid #dbe7f3; font-size:8.5px; letter-spacing:0.24em; color:#334155; text-transform:uppercase; font-weight:700;">
                        Display Information
                    </span>
                </div>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                    @foreach($rightFacts as $fact)
                        <tr>
                            <td width="48%" style="padding:6px 10px; border-bottom:1px solid #e5e7eb; font-size:9.5px; color:#334155;">{{ $fact['label'] ?? '-' }}</td>
                            <td width="52%" style="padding:6px 10px; border-bottom:1px solid #e5e7eb; background:#f8fafc; font-size:9.5px; color:#0f172a;">{{ $fact['value'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                    @foreach($footerFacts as $fact)
                        <tr>
                            <td width="48%" style="padding:6px 10px; border-bottom:1px solid #e5e7eb; font-size:9.5px; color:#334155;">{{ $fact['label'] ?? '-' }}</td>
                            <td width="52%" style="padding:6px 10px; border-bottom:1px solid #e5e7eb; background:#f8fafc; font-size:9.5px; color:#0f172a;">{{ $fact['value'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    <div style="border-top:1px solid #cbd5e1; margin-bottom:12px;"></div>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-bottom:8px;">
        <tr>
            <td width="72%" style="font-size:17px; font-weight:700; color:#0f172a;">{{ $report['name'] ?? 'History Report' }}</td>
            <td width="28%" align="right" style="font-size:8.5px; letter-spacing:0.2em; text-transform:uppercase; color:#64748b;">Result <span style="font-size:13px; font-weight:700; color:{{ $resultColor }}; margin-left:8px;">{{ $resultLabel }}</span></td>
        </tr>
    </table>

    @if($summaryRows->isNotEmpty())
        <div style="font-size:8.5px; letter-spacing:0.22em; text-transform:uppercase; color:#64748b; margin-bottom:8px;">Summary</div>
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-bottom:16px;">
            <tr>
                <td style="padding:7px 8px; border:1px solid #94a3b8; background:#eef2f7; font-size:9.5px; font-weight:700; color:#0f172a;" width="70%">Test</td>
                <td style="padding:7px 8px; border:1px solid #94a3b8; background:#eef2f7; font-size:9.5px; font-weight:700; color:#0f172a;" width="30%" align="center">Result</td>
            </tr>
            @foreach($summaryRows as $row)
                @php
                    $tone = $row['tone'] ?? 'neutral';
                    $answerColor = match ($tone) {
                        'success' => '#15803d',
                        'danger' => '#b91c1c',
                        'warning' => '#b45309',
                        default => '#334155',
                    };
                @endphp
                <tr>
                    <td style="padding:7px 8px; border:1px solid #cbd5e1; font-size:9.5px; color:#0f172a;">{{ $row['question'] ?? '-' }}</td>
                    <td style="padding:7px 8px; border:1px solid #cbd5e1; font-size:9.5px; font-weight:700; color:{{ $answerColor }};" align="center">{{ strtoupper((string) ($row['answer'] ?? '-')) }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    @if($sections->isNotEmpty())
        <pagebreak />

        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-bottom:14px;">
            <tr>
                <td width="70%" style="font-size:8.5px; letter-spacing:0.2em; text-transform:uppercase; color:#64748b;">Question Review</td>
                <td width="30%" align="right" style="font-size:8.5px; letter-spacing:0.2em; text-transform:uppercase; color:#64748b;">Detailed Responses</td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top:6px; font-size:15px; font-weight:700; color:#0f172a;">{{ $report['name'] ?? 'History Report' }}</td>
            </tr>
        </table>

        @foreach($sections as $sectionIndex => $section)
            <div style="font-size:8.5px; letter-spacing:0.18em; text-transform:uppercase; color:#64748b; margin-bottom:6px; margin-top:{{ $sectionIndex === 0 ? '0' : '12px' }};">{{ $section['name'] ?? ('Section ' . ($sectionIndex + 1)) }}</div>

            @foreach(($section['questions'] ?? []) as $question)
                @php
                    $tone = $question['tone'] ?? 'neutral';
                    $answerColor = match ($tone) {
                        'success' => '#15803d',
                        'danger' => '#b91c1c',
                        'warning' => '#b45309',
                        default => '#334155',
                    };
                @endphp
                <div style="background:#eef2f7; border:1px solid #cbd5e1; padding:6px 8px; font-size:9.5px; font-weight:700; color:#0f172a; margin-top:8px;">
                    {{ $question['text'] ?? '-' }}
                </div>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-bottom:10px; page-break-inside:avoid;">
                    <tr>
                        <td width="86%" style="padding:7px 8px; border:1px solid #cbd5e1; font-size:9.5px; color:#0f172a;">{{ $question['text'] ?? '-' }}</td>
                        <td width="14%" align="center" style="padding:7px 8px; border:1px solid #cbd5e1; font-size:9.5px; font-weight:700; color:{{ $answerColor }};">{{ strtolower((string) ($question['answer'] ?? '-')) }}</td>
                    </tr>
                </table>
            @endforeach

            @if(!empty($section['comment']))
                <div style="background:#fff7ed; border:1px solid #fed7aa; padding:7px 9px; font-size:9.5px; color:#7c2d12; margin-bottom:10px;">
                    <span style="font-weight:700;">Comment:</span> {{ $section['comment'] }}
                </div>
            @endif

            @if(!empty($section['scores']) && count($section['scores']))
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-bottom:12px; page-break-inside:avoid;">
                    <tr>
                        <td style="padding:7px 8px; border:1px solid #94a3b8; background:#eef2f7; font-size:9.5px; font-weight:700;" width="42%">Metric</td>
                        <td style="padding:7px 8px; border:1px solid #94a3b8; background:#eef2f7; font-size:9.5px; font-weight:700;" width="18%">Limit</td>
                        <td style="padding:7px 8px; border:1px solid #94a3b8; background:#eef2f7; font-size:9.5px; font-weight:700;" width="20%">Measured</td>
                        <td style="padding:7px 8px; border:1px solid #94a3b8; background:#eef2f7; font-size:9.5px; font-weight:700;" width="20%">Status</td>
                    </tr>
                    @foreach($section['scores'] as $score)
                        @php
                            $scoreTone = $score['statusTone'] ?? 'neutral';
                            $scoreColor = match ($scoreTone) {
                                'success' => '#15803d',
                                'danger' => '#b91c1c',
                                'warning' => '#b45309',
                                default => '#334155',
                            };
                        @endphp
                        <tr>
                            <td style="padding:7px 8px; border:1px solid #cbd5e1; font-size:9.5px;">{{ $score['name'] ?? '-' }}</td>
                            <td style="padding:7px 8px; border:1px solid #cbd5e1; font-size:9.5px;">{{ $score['limit'] ?? '-' }}</td>
                            <td style="padding:7px 8px; border:1px solid #cbd5e1; font-size:9.5px;">{{ $score['measured'] ?? '-' }}</td>
                            <td style="padding:7px 8px; border:1px solid #cbd5e1; font-size:9.5px; font-weight:700; color:{{ $scoreColor }};">{{ $score['statusLabel'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        @endforeach
    @endif
</body>
</html>
