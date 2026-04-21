<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $report['title'] ?? 'PerfectLum Report' }}</title>
</head>
<body style="margin:0; padding:0; font-family: dejavusans, sans-serif; color:#0f172a; font-size:9.5px; line-height:1.42; background:#ffffff;">
    @php
        $columns = collect($report['columns'] ?? []);
        $rows = collect($report['rows'] ?? []);
        $columnWidth = $columns->count() > 0 ? round(100 / $columns->count(), 2) : 100;
    @endphp

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-bottom:14px;">
        <tr>
            <td width="56%" valign="middle">
                @if(!empty($logoPerfectLum))
                    <img src="{{ $logoPerfectLum }}" alt="PerfectLum" style="height:38px; display:block;">
                @else
                    <div style="font-size:24px; font-weight:700; color:#0f172a;">PerfectLum</div>
                @endif
            </td>
            <td width="44%" align="right" valign="middle">
                @if(!empty($logoQubyx))
                    <img src="{{ $logoQubyx }}" alt="QUBYX" style="height:24px; display:inline-block;">
                @else
                    <div style="font-size:20px; font-weight:700; color:#111827;">QUBYX</div>
                @endif
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-bottom:16px;">
        <tr>
            <td width="70%" valign="top" style="padding-right:18px;">
                <div style="font-size:8.5px; letter-spacing:0.24em; color:#64748b; text-transform:uppercase; margin-bottom:7px;">
                    {{ $report['eyebrow'] ?? 'PerfectLum Report' }}
                </div>
                <div style="font-size:20px; font-weight:700; line-height:1.18; color:#0f172a; margin-bottom:8px;">
                    {{ $report['title'] ?? 'Report' }}
                </div>
                <div style="font-size:9.5px; color:#475569; max-width:520px; line-height:1.45;">
                    {{ $report['subtitle'] ?? 'Exported workspace report.' }}
                </div>
            </td>
            <td width="30%" valign="top" align="right">
                <table width="92%" align="right" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; background:#f8fafc; border:1px solid #dbe3ee;">
                    <tr>
                        <td style="padding:10px 12px 0 12px; font-size:8.5px; letter-spacing:0.18em; color:#64748b; text-transform:uppercase;" align="right">Generated</td>
                    </tr>
                    <tr>
                        <td style="padding:3px 12px 4px 12px; font-size:13px; font-weight:700; color:#0f172a;" align="right">{{ $report['generatedAt'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:0 12px 12px 12px; font-size:9.5px; color:#475569;" align="right">
                            {{ $report['total'] ?? $rows->count() }} records
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="border-top:1px solid #cbd5e1; margin-bottom:12px;"></div>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
        <tr>
            @foreach($columns as $column)
                <td width="{{ $columnWidth }}%" style="padding:8px 7px; border:1px solid #94a3b8; background:#eef2f7; font-size:8.5px; letter-spacing:0.14em; text-transform:uppercase; font-weight:700; color:#334155;">
                    {{ $column }}
                </td>
            @endforeach
        </tr>

        @forelse($rows as $rowIndex => $row)
            <tr>
                @foreach($columns as $columnIndex => $column)
                    @php
                        $cell = $row[$columnIndex] ?? '-';
                        $tone = is_array($cell) ? ($cell['tone'] ?? 'neutral') : 'neutral';
                        $value = is_array($cell) ? ($cell['value'] ?? '-') : $cell;
                        $color = match ($tone) {
                            'success' => '#15803d',
                            'danger' => '#b91c1c',
                            'warning' => '#b45309',
                            default => '#0f172a',
                        };
                        $bg = $rowIndex % 2 === 0 ? '#ffffff' : '#f8fafc';
                    @endphp
                    <td width="{{ $columnWidth }}%" style="padding:8px 7px; border:1px solid #d7dee8; background:{{ $bg }}; font-size:9.3px; color:{{ $color }}; vertical-align:top; font-weight:{{ $tone === 'neutral' ? '400' : '700' }};">
                        {{ trim(strip_tags((string) $value)) !== '' ? trim(strip_tags((string) $value)) : '-' }}
                    </td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ max(1, $columns->count()) }}" style="padding:18px 10px; border:1px solid #d7dee8; font-size:10px; color:#64748b; text-align:center;">
                    No records found
                </td>
            </tr>
        @endforelse
    </table>
</body>
</html>
