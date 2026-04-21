<?php

namespace App\Exports;

class HistoriesReportsExport extends StyledReportExport
{
    private $_data, $_from, $_to, $_site;

    public function __construct($data, $from, $to, $site) {
        $this->_data = $data;
        $this->_from = $from;
        $this->_to = $to;
        $this->_site = $site;
    }

    protected function report(): array
    {
        $rows = $this->collection($this->_data);

        return [
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
                $this->statusText($history->result_desc ?: '-'),
            ])->values()->all(),
        ];
    }
}
