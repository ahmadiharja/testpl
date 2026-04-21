<?php

namespace App\Exports;

class AllDisplaysExport extends StyledReportExport
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
            'title' => 'All Displays',
            'subtitle' => 'Connected display inventory exported from the current workspace scope.',
            'generatedAt' => now()->format('d M Y H:i'),
            'total' => $rows->count(),
            'columns' => ['Display', 'Inventory Number', 'Workstation', 'Workgroup', 'Facility', 'Status'],
            'rows' => $rows->map(fn($display) => [
                $this->displayName($display),
                $this->preferenceValue($display, 'InventoryNumber'),
                optional($display->workstation)->name ?: '-',
                optional(optional($display->workstation)->workgroup)->name ?: '-',
                optional(optional(optional($display->workstation)->workgroup)->facility)->name ?: '-',
                (string) ($display->status ?? '') === '1' ? 'OK' : 'Failed',
            ])->values()->all(),
        ];
    }
}
