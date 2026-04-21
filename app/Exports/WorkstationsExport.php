<?php

namespace App\Exports;

class WorkstationsExport extends StyledReportExport
{
    private $_data, $_from, $_to, $_site;

    public function __construct($data, $from, $to, $site = []) {
        $this->_data = $data;
        $this->_from = $from;
        $this->_to = $to;
        $this->_site = $site;
    }

    protected function report(): array
    {
        $rows = $this->collection($this->_data);

        return [
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
            ])->values()->all(),
        ];
    }
}
