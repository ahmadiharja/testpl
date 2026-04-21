<?php

namespace App\Exports;

class DisplayCalibrationExport extends StyledReportExport
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
            'title' => 'Display Calibration Schedules',
            'subtitle' => 'Calibration tasks exported from the selected display scope.',
            'generatedAt' => now()->format('d M Y H:i'),
            'total' => $rows->count(),
            'columns' => ['Display', 'Workstation', 'Workgroup', 'Facility', 'Task Type', 'Schedule Type', 'Due Date'],
            'rows' => $rows->map(fn($task) => [
                $this->displayName($task->display),
                optional(optional($task->display)->workstation)->name ?: '-',
                optional(optional(optional($task->display)->workstation)->workgroup)->name ?: '-',
                optional(optional(optional(optional($task->display)->workstation)->workgroup)->facility)->name ?: '-',
                optional($task->taskType)->title ?: ($task->type ?? '-'),
                optional($task->ScheduleType)->title ?: ($task->schtype ?? '-'),
                $this->dueDateForTask($task),
            ])->values()->all(),
        ];
    }
}
