<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class StyledReportExport implements FromArray, ShouldAutoSize, WithEvents, WithStyles, WithTitle
{
    private ?array $cachedReport = null;

    abstract protected function report(): array;

    public function array(): array
    {
        $report = $this->compiledReport();
        $columns = array_values($report['columns'] ?? ['Name']);
        $rows = [
            [$report['title'] ?? 'PerfectLum Report'],
            [$report['subtitle'] ?? 'Exported workspace report.'],
            ['Generated', $report['generatedAt'] ?? now()->format('d M Y H:i'), 'Records', (string) ($report['total'] ?? count($report['rows'] ?? []))],
            [],
            $columns,
        ];

        foreach (($report['rows'] ?? []) as $row) {
            $rows[] = array_map(fn ($cell) => $this->cellValue($cell), array_values($row));
        }

        return $rows;
    }

    public function title(): string
    {
        return mb_substr(preg_replace('/[\\\\\\/\\?\\*\\[\\]:]+/', ' ', $this->compiledReport()['title'] ?? 'Report'), 0, 31);
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $report = $this->compiledReport();
                $columns = array_values($report['columns'] ?? ['Name']);
                $columnCount = max(1, count($columns));
                $lastColumn = Coordinate::stringFromColumnIndex($columnCount);
                $lastRow = max(5, 5 + count($report['rows'] ?? []));

                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->mergeCells("A2:{$lastColumn}2");

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('0F172A');
                $sheet->getStyle('A2')->getFont()->setSize(11)->getColor()->setRGB('64748B');
                $sheet->getStyle('A3:D3')->getFont()->setBold(true)->getColor()->setRGB('334155');

                $sheet->getStyle("A5:{$lastColumn}5")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '334155']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EAF3FB']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D7DEE8']],
                    ],
                ]);

                if ($lastRow >= 6) {
                    $sheet->getStyle("A6:{$lastColumn}{$lastRow}")->applyFromArray([
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_TOP,
                            'wrapText' => true,
                        ],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                        ],
                    ]);

                    foreach (range(6, $lastRow) as $rowNumber) {
                        if ($rowNumber % 2 === 0) {
                            $sheet->getStyle("A{$rowNumber}:{$lastColumn}{$rowNumber}")
                                ->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setRGB('F8FAFC');
                        }

                        foreach (range(1, $columnCount) as $columnNumber) {
                            $address = Coordinate::stringFromColumnIndex($columnNumber) . $rowNumber;
                            $value = strtolower((string) $sheet->getCell($address)->getValue());

                            if ($value === 'ok') {
                                $sheet->getStyle($address)->getFont()->setBold(true)->getColor()->setRGB('15803D');
                            } elseif (str_contains($value, 'failed') || str_contains($value, 'cancel')) {
                                $sheet->getStyle($address)->getFont()->setBold(true)->getColor()->setRGB('B91C1C');
                            } elseif (str_contains($value, 'skipped') || str_contains($value, 'warning')) {
                                $sheet->getStyle($address)->getFont()->setBold(true)->getColor()->setRGB('B45309');
                            }
                        }
                    }
                }

                $sheet->freezePane('A6');
                $sheet->setAutoFilter("A5:{$lastColumn}{$lastRow}");
                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(5)->setRowHeight(24);
            },
        ];
    }

    protected function compiledReport(): array
    {
        if ($this->cachedReport === null) {
            $this->cachedReport = $this->report();
        }

        return $this->cachedReport;
    }

    protected function displayName($display): string
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

    protected function preferenceValue($model, string $key, string $fallback = '-'): string
    {
        if ($model && method_exists($model, 'preference')) {
            $value = trim((string) $model->preference($key));
            return $value !== '' ? $value : $fallback;
        }

        return $fallback;
    }

    protected function statusText($value): string
    {
        return trim(strip_tags((string) $value)) ?: '-';
    }

    protected function dueDateForTask($task): string
    {
        if (!$task || empty($task->nextrun) || (int) $task->nextrun <= 0) {
            return 'Never';
        }

        $timezone = optional(optional(optional(optional($task->display)->workstation)->workgroup)->facility)->timezone
            ?: config('app.timezone', 'UTC');

        return Carbon::createFromTimestamp((int) $task->nextrun, $timezone)->format('d M Y H:i');
    }

    protected function formatHistoryTime($history): string
    {
        if (!$history || empty($history->time)) {
            return '-';
        }

        $timezone = optional(optional(optional(optional($history->display)->workstation)->workgroup)->facility)->timezone
            ?: config('app.timezone', 'UTC');

        return Carbon::createFromTimestampUTC((int) $history->time)->setTimezone($timezone)->format('d M Y H:i');
    }

    protected function formatReportDate($value): string
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

    private function cellValue($cell): string
    {
        if (is_array($cell)) {
            $cell = $cell['value'] ?? '';
        }

        return trim(strip_tags((string) $cell)) ?: '-';
    }

    protected function collection($data): Collection
    {
        return $data instanceof Collection ? $data : collect($data);
    }
}
