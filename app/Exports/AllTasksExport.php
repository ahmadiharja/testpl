<?php

namespace App\Exports;

class AllTasksExport extends StyledReportExport
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
            'title' => 'All Calibration and QA Schedules',
            'subtitle' => 'Scheduled task list exported from the current workspace scope.',
            'generatedAt' => now()->format('d M Y H:i'),
            'total' => $rows->count(),
            'columns' => ['Display', 'Workstation', 'Workgroup', 'Facility', 'Task Type', 'Schedule Type', 'Due Date'],
            'rows' => $rows->map(fn($task) => [
                $task['display_model'] ?? '-',
                $task['workstation'] ?? '-',
                $task['workgroup'] ?? '-',
                $task['facility'] ?? '-',
                $task['name'] ?? '-',
                $task['schtype'] ?? '-',
                $this->formatReportDate($task['duedate'] ?? ($task['due_date_sort'] ?? null)),
            ])->values()->all(),
        ];
    }
}
