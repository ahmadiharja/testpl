<?php

namespace App\Exports;

class WorkgroupsExport extends StyledReportExport
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
            'title' => 'Workgroups',
            'subtitle' => 'Department and workgroup list exported from the current workspace scope.',
            'generatedAt' => now()->format('d M Y H:i'),
            'total' => $rows->count(),
            'columns' => ['Name', 'Address', 'Phone', 'Facility'],
            'rows' => $rows->map(fn($workgroup) => [
                $workgroup->name ?? '-',
                $workgroup->address ?? '-',
                $workgroup->phone ?? '-',
                optional($workgroup->facility)->name ?: '-',
            ])->values()->all(),
        ];
    }
}
