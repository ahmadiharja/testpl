<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\QATask;
use App\Models\History;
use App\Models\ScheduleType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    protected function positiveInt($value, int $fallback = 0): int
    {
        $value = (int) $value;

        return $value > 0 ? $value : $fallback;
    }

    protected function taskDayOfWeekList(Task $task): array
    {
        return collect((array) $task->dayofweek)
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value >= 1 && $value <= 7)
            ->values()
            ->all();
    }

    protected function taskMonthList(Task $task): array
    {
        return collect((array) $task->lmonthes)
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value >= 1 && $value <= 12)
            ->values()
            ->all();
    }

    protected function taskOccursOnDate(Task $task, Carbon $date, Carbon $startDate): bool
    {
        if ($date->lt($startDate)) {
            return false;
        }

        switch ((int) $task->schtype) {
            case ScheduleType::STARTUP:
                return false;

            case ScheduleType::ONCE:
                return $date->isSameDay($startDate);

            case ScheduleType::DAILY:
                if ((int) $task->nthflag === 0) {
                    $interval = $this->positiveInt($task->ndays, 1);
                    return $startDate->diffInDays($date) % $interval === 0;
                }

                $days = $this->taskDayOfWeekList($task);
                return empty($days) || in_array($date->dayOfWeekIso, $days, true);

            case ScheduleType::WEEKLY:
                $days = $this->taskDayOfWeekList($task);
                if (!empty($days) && !in_array($date->dayOfWeekIso, $days, true)) {
                    return false;
                }

                $interval = $this->positiveInt($task->nweeks, 1);
                $startWeek = $startDate->copy()->startOfWeek(Carbon::MONDAY);
                $currentWeek = $date->copy()->startOfWeek(Carbon::MONDAY);
                $weeksDiff = intdiv($startWeek->diffInDays($currentWeek), 7);

                return $weeksDiff % $interval === 0;

            case ScheduleType::MONTHLY:
                $allowedMonths = $this->taskMonthList($task);
                if (!empty($allowedMonths) && !in_array($date->month, $allowedMonths, true)) {
                    return false;
                }

                if ((int) $task->nthflag === 1) {
                    $dayOfMonth = $this->positiveInt($task->monthday, $startDate->day);
                    return $date->day === min($dayOfMonth, $date->daysInMonth);
                }

                $monthWeek = $this->positiveInt($task->monthweek, 0);
                $days = $this->taskDayOfWeekList($task);
                if ($monthWeek <= 0 || empty($days)) {
                    return false;
                }

                return $date->weekOfMonth === $monthWeek && in_array($date->dayOfWeekIso, $days, true);

            case ScheduleType::QUARTERLY:
            case ScheduleType::SEMIANNUAL:
            case ScheduleType::ANNUAL:
                $monthStep = match ((int) $task->schtype) {
                    ScheduleType::QUARTERLY => 3,
                    ScheduleType::SEMIANNUAL => 6,
                    ScheduleType::ANNUAL => 12,
                    default => 1,
                };

                $monthsDiff = (($date->year - $startDate->year) * 12) + ($date->month - $startDate->month);
                if ($monthsDiff < 0 || $monthsDiff % $monthStep !== 0) {
                    return false;
                }

                return $date->day === min($startDate->day, $date->daysInMonth);

            default:
                return false;
        }
    }

    protected function scheduledTaskOccurrenceTimestamps(Task $task, int $rangeStart, int $rangeEnd, string $displayTimezone): array
    {
        $startDateTime = $task->startdatetime?->copy();
        if (!$startDateTime) {
            return [];
        }

        $startDateTime = $startDateTime->setTimezone($displayTimezone);
        $rangeStartDay = Carbon::createFromTimestampUTC($rangeStart)->setTimezone($displayTimezone)->startOfDay();
        $rangeEndDay = Carbon::createFromTimestampUTC($rangeEnd)->setTimezone($displayTimezone)->endOfDay();

        if ((int) $task->schtype === ScheduleType::STARTUP) {
            return [];
        }

        if ((int) $task->schtype === ScheduleType::ONCE) {
            if ($startDateTime->betweenIncluded($rangeStartDay, $rangeEndDay)) {
                return [$startDateTime->getTimestamp()];
            }

            return [];
        }

        $cursor = $rangeStartDay->copy();
        $occurrences = [];
        $startDate = $startDateTime->copy()->startOfDay();

        while ($cursor->lte($rangeEndDay)) {
            if ($this->taskOccursOnDate($task, $cursor, $startDate)) {
                $occurrenceAt = $cursor->copy()->setTime($startDateTime->hour, $startDateTime->minute, $startDateTime->second);

                if ($occurrenceAt->betweenIncluded($rangeStartDay, $rangeEndDay)) {
                    $occurrences[] = $occurrenceAt->getTimestamp();
                }
            }

            $cursor->addDay();
        }

        return $occurrences;
    }

    protected function formatAbsoluteIso(?int $timestamp): ?string
    {
        if (!$timestamp || (int) $timestamp <= 0) {
            return null;
        }

        return Carbon::createFromTimestampUTC((int) $timestamp)->toIso8601String();
    }

    protected function formatClientWallClockIso(?int $timestamp): ?string
    {
        if (!$timestamp || (int) $timestamp <= 0) {
            return null;
        }

        return Carbon::createFromTimestampUTC((int) $timestamp)->format('Y-m-d\TH:i:s');
    }

    protected function staleQaTaskCutoff(): string
    {
        return now()->subDays(60)->toDateTimeString();
    }

    protected function resultLabel(?int $result): string
    {
        return match ((int) $result) {
            2 => 'Passed',
            3 => 'Failed',
            4 => 'Skipped',
            5 => 'Canceled',
            default => 'Unknown',
        };
    }

    protected function resultTone(?int $result): string
    {
        return match ((int) $result) {
            2 => 'success',
            3 => 'danger',
            4, 5 => 'warning',
            default => 'neutral',
        };
    }

    protected function latestDisplayHistorySummary(int $displayId, string $timezone, array &$cache): ?array
    {
        if (array_key_exists($displayId, $cache)) {
            return $cache[$displayId];
        }

        $history = History::query()
            ->where('display_id', $displayId)
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->first(['id', 'name', 'time', 'result', 'regulation', 'classification']);

        if (!$history) {
            return $cache[$displayId] = null;
        }

        return $cache[$displayId] = $this->buildHistorySummaryPayload($history, $timezone);
    }

    protected function buildHistorySummaryPayload(History $history, string $timezone): array
    {
        return [
            'id' => (int) $history->id,
            'name' => trim((string) $history->name) !== '' ? $history->name : 'History Report',
            'performedAtTs' => (int) $history->time,
            'performedAtIso' => $this->formatClientWallClockIso((int) $history->time),
            'performedAt' => Carbon::createFromTimestampUTC((int) $history->time)->format('d M Y H:i'),
            'resultLabel' => $this->resultLabel((int) $history->result),
            'resultTone' => $this->resultTone((int) $history->result),
            'regulation' => trim((string) $history->regulation) !== '' ? $history->regulation : null,
            'classification' => trim((string) $history->classification) !== '' ? $history->classification : null,
            'reportUrl' => url('histories/' . $history->id),
            'printUrl' => url('histories/' . $history->id . '/preview'),
        ];
    }

    public function index()
    {
        return view("calendar.index");
    }

    public function events(Request $request) {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);

        $role=$request->session()->get('role');

        // get timezone
        $timezone = $user->timezone;
        $start = Carbon::parse($request->input('start'),  $timezone)->getTimestamp();
        $end = Carbon::parse($request->input('end'),  $timezone)->getTimestamp();

        $res = [];
        $historySummaryCache = [];
        if ($role!='super') {
            //$tasksNextrun = auth()->user()->facility->tasks()->whereBetween('nextrun', [$start, $end])->whereNotIn('schtype', [2])->get();
            $tasksNextrun = $user->facility->tasks()
                ->has('display')
                ->where('tasks.deleted', 0)
                ->where('tasks.disabled', 0)
                ->whereBetween('tasks.nextrun', [$start, $end])
                ->get();
        } else {
            //$tasksNextrun = Task::whereBetween('nextrun', [$start, $end])->whereNotIn('schtype', [2])->get();
            $tasksNextrun = Task::has('display')
                ->where('tasks.deleted', 0)
                ->where('tasks.disabled', 0)
                ->whereBetween('tasks.nextrun', [$start, $end])
                ->get();
        }
        $tasksNextrun = $tasksNextrun->filter(function($task) {
            return $task->display->preference('exclude')==0;
        });
        foreach ($tasksNextrun as $task) {
            if($task->taskType==NULL) Log::info('TaskType NULL for Task ID: '.$task->id);
            $displayTimezone = $task->display->workstation->workgroup->facility->timezone ?: $timezone;
            $latestHistory = $this->latestDisplayHistorySummary((int) $task->display->id, $displayTimezone, $historySummaryCache);
            $data = [
                'display' => $task->display->serial,
                'workstation' => $task->display->workstation->name,
                'workgroup' => $task->display->workstation->workgroup->name,
                'facility' => $task->display->workstation->workgroup->facility->name,
                'tasktype' => $task->taskType->title,
                'testpattern' => $task->testPattern->title,
                'schtype' => $task->scheduleType->title,
                'startdate' => $task->startdate,
                'lastrun' => $task->lastrun,
                'nextrun' => $task->getNextrunString(),
                'disabled' => $task->disabledText,
                'status' => $task->statusText,
                'taskid' => $task->id,
                'isqa' => 0,
                'eventKind' => 'scheduled',
                'eventKindLabel' => 'Scheduled Task',
                'scheduleLabel' => $task->scheduleType->title ?? 'Manual',
                'lastRunTs' => (int) ($task->lastrun ?? 0),
                'lastRunIso' => $this->formatAbsoluteIso((int) ($task->lastrun ?? 0)),
                'nextRunTs' => (int) ($task->nextrun ?? 0),
                'nextRunIso' => $this->formatAbsoluteIso((int) ($task->nextrun ?? 0)),
                'lastRunLabel' => (int) ($task->lastrun ?? 0) > 0
                    ? Carbon::createFromTimestampUTC((int) $task->lastrun)->setTimezone($displayTimezone)->format('d M Y H:i')
                    : 'Not recorded',
                'nextRunLabel' => $task->getNextrunString(),
                'statusLabel' => strip_tags((string) $task->statusText) ?: 'Unknown',
                'statusTone' => $this->resultTone((int) ($task->status ?? 0) === 0 ? 2 : 3),
                'historySummary' => $latestHistory,
                'historyReportsUrl' => url('histories-reports?display_id=' . $task->display->id),
                'schedulerUrl' => url('scheduler?display_id=' . $task->display->id),
                'displayCalibrationUrl' => url('display-calibration?display_id=' . $task->display->id),

            ];
            if ($task->display && $task->display->preference('exclude') ) continue;
            $ws_info = $user->hasRole('super')?"\n(".$task->display->workstation->name."/".$task->display->model.")":'';
            $occurrenceTimestamps = $this->scheduledTaskOccurrenceTimestamps($task, $start, $end, $displayTimezone);

            foreach ($occurrenceTimestamps as $occurrenceTimestamp) {
                $occurrenceData = $data;
                $occurrenceData['nextRunTs'] = $occurrenceTimestamp;
                $occurrenceData['nextRunIso'] = $this->formatAbsoluteIso($occurrenceTimestamp);
                $occurrenceData['nextRunLabel'] = Carbon::createFromTimestampUTC($occurrenceTimestamp)
                    ->setTimezone($displayTimezone)
                    ->format('d M Y H:i');

                $res[] = [
                    'id' => 'task_next_' . $task->id . '_' . $occurrenceTimestamp,
                    'title' => $task->taskType->title.$ws_info,
                    'start' => $this->formatAbsoluteIso($occurrenceTimestamp),
                    'allDay' => false,
                    'editable' => false,
                    'className'=> $task->statusEventColor,
                    'data' => $occurrenceData,
                ];
            }
        }

        /*if (auth()->user()->hasRole('admin')) {
            $tasksLastrun = auth()->user()->facility->tasks()->whereBetween('lastrun', [date($start), date($end)])->whereNotIn('schtype', [2])->get();
        } else {
            $tasksLastrun = Task::whereBetween('lastrun', [date($start), date($end)])->whereNotIn('schtype', [2])->get();
        }
        
        foreach ($tasksLastrun as $task) {
            $data = [
                'display' => $task->display->serial,
                'workstation' => $task->display->workstation->name,
                'workgroup' => $task->display->workstation->workgroup->name,
                'facility' => $task->display->workstation->workgroup->facility->name,
                'tasktype' => $task->taskType->title,
                'testpattern' => $task->testPattern->title,
                'schtype' => $task->scheduleType->title,
                'startdate' => $task->startdate,
                'lastrun' => $task->lastrun,
                'nextrun' => $task->nextrun,
                'disabled' => $task->disabledText,
                'status' => $task->statusText,


            ];
            $res[] = [
                'id' => 'task_last_' . $task->id,
                'title' => $task->taskType->title,
                'start' => $task->lastrun,
                'allDay' => false,
                'editable' => false,
                'url' => '/tasks/'.$task->id,
                'className'=> 'event-blue',
                'display' => $task->display->serial,
                'data' => $data,
            ];
        }*/

        $qaTaskCutoff = $this->staleQaTaskCutoff();

        // Get QA Tasks
        if ($role!='super') {
            $qatasks = $user->facility->qatasks()
                ->has('display')
                ->where('qa_tasks.deleted', 0)
                ->whereNull('qa_tasks.deleted_at')
                ->whereBetween('qa_tasks.nextdate', [$start, $end])
                ->where(function ($query) use ($qaTaskCutoff) {
                    $query->where('qa_tasks.updated_at', '>=', $qaTaskCutoff)
                        ->orWhereHas('display.workstation', function ($workstationQuery) use ($qaTaskCutoff) {
                            $workstationQuery->whereNotNull('last_connected')
                                ->where('last_connected', '>=', $qaTaskCutoff);
                        });
                })
                ->get();
        } else {
            $qatasks = QATask::has('display')
                ->where('qa_tasks.deleted', 0)
                ->whereNull('qa_tasks.deleted_at')
                ->whereBetween('qa_tasks.nextdate', [$start, $end])
                ->where(function ($query) use ($qaTaskCutoff) {
                    $query->where('qa_tasks.updated_at', '>=', $qaTaskCutoff)
                        ->orWhereHas('display.workstation', function ($workstationQuery) use ($qaTaskCutoff) {
                            $workstationQuery->whereNotNull('last_connected')
                                ->where('last_connected', '>=', $qaTaskCutoff);
                        });
                })
                ->get();
        }
        $qatasks = $qatasks->filter(function($task) {
            return $task->display->preference('exclude')==0;
        });
        foreach ($qatasks as $task) {
            $displayTimezone = $task->display->workstation->workgroup->facility->timezone ?: $timezone;
            $latestHistory = $this->latestDisplayHistorySummary((int) $task->display->id, $displayTimezone, $historySummaryCache);
            $data = [
                'display' => $task->display->serial,
                'workstation' => $task->display->workstation->name,
                'workgroup' => $task->display->workstation->workgroup->name,
                'facility' => $task->display->workstation->workgroup->facility->name,
                'tasktype' => $task->name,
                'testpattern' => '',
                'schtype' => $task->freq,
                'startdate' => Carbon::createFromTimeStamp($task->nextdate, $timezone)->format('Y-m-d'),
                'lastrun' => $task->lastrundate,
                'nextrun' => $task->nextrun,
                'disabled' => $task->disabledText,
                'status' => $task->statusText,
                'taskid' => $task->id,
                'isqa' => 1,
                'eventKind' => 'scheduled',
                'eventKindLabel' => 'Scheduled Task',
                'scheduleLabel' => $task->freq ?: 'QA Task',
                'lastRunTs' => (int) ($task->lastrundate ?? 0),
                'lastRunIso' => $this->formatAbsoluteIso((int) ($task->lastrundate ?? 0)),
                'nextRunTs' => (int) ($task->nextdate ?? 0),
                'nextRunIso' => $this->formatAbsoluteIso((int) ($task->nextdate ?? 0)),
                'lastRunLabel' => (int) ($task->lastrundate ?? 0) > 0
                    ? Carbon::createFromTimestampUTC((int) $task->lastrundate)->setTimezone($displayTimezone)->format('d M Y H:i')
                    : 'Not recorded',
                'nextRunLabel' => Carbon::createFromTimestampUTC((int) $task->nextdate)->setTimezone($displayTimezone)->format('d M Y'),
                'statusLabel' => strip_tags((string) $task->statusText) ?: 'Unknown',
                'statusTone' => $this->resultTone((int) ($task->taskStatus ?? 0) === 0 ? 2 : 3),
                'historySummary' => $latestHistory,
                'historyReportsUrl' => url('histories-reports?display_id=' . $task->display->id),
                'schedulerUrl' => url('scheduler?display_id=' . $task->display->id),
                'displayCalibrationUrl' => url('display-calibration?display_id=' . $task->display->id),
            ];
            $res[] = [
                'id' => 'qa_task_' . $task->id,
                'title' => $task->name,
                'start' => Carbon::createFromTimeStamp($task->nextdate, $timezone)->format('Y-m-d'),
                'allDay' => true,
                'editable' => false,
               // 'url' => '/qatasks/'.$task->id,
                'className'=> 'event-blue',
                'backgroundColor' => 'blue',
                'display' => $task->display->serial,
                'data' => $data,
            ];
        }

        $historyQuery = History::query()
            ->has('display')
            ->whereBetween('time', [$start, $end])
            ->whereIn('result', [2, 3, 4, 5]);

        if ($role != 'super') {
            $historyQuery->whereHas('display.workstation.workgroup.facility', function ($query) use ($user) {
                $query->where('id', $user->facility_id);
            });
        }

        $historyItems = $historyQuery
            ->with('display.workstation.workgroup.facility')
            ->orderBy('time')
            ->get()
            ->filter(function ($history) {
                return $history->display && $history->display->preference('exclude') == 0;
            });

        foreach ($historyItems as $history) {
            $display = $history->display;
            $displayTimezone = $display->workstation->workgroup->facility->timezone ?: $timezone;
            $historySummary = $this->buildHistorySummaryPayload($history, $displayTimezone);
            $locationBits = [
                $display->workstation->workgroup->facility->name,
                $display->workstation->workgroup->name,
                $display->workstation->name,
                $display->serial,
            ];

            $res[] = [
                'id' => 'history_' . $history->id,
                'title' => $historySummary['name'],
                'start' => $this->formatClientWallClockIso((int) $history->time),
                'allDay' => false,
                'editable' => false,
                'className' => match ((int) $history->result) {
                    2 => 'event-green',
                    3 => 'event-red',
                    4, 5 => 'event-yellow',
                    default => 'event-blue',
                },
                'data' => [
                    'display' => $display->serial,
                    'workstation' => $display->workstation->name,
                    'workgroup' => $display->workstation->workgroup->name,
                    'facility' => $display->workstation->workgroup->facility->name,
                    'tasktype' => trim((string) $history->name) !== '' ? $history->name : 'History Report',
                    'testpattern' => '',
                    'schtype' => 'Completed',
                    'startdate' => Carbon::createFromTimestampUTC((int) $history->time)->setTimezone($displayTimezone)->format('Y-m-d'),
                    'lastrun' => $history->time,
                    'nextrun' => null,
                    'disabled' => 'Enabled',
                    'status' => $historySummary['resultLabel'],
                    'taskid' => $history->id,
                    'isqa' => 1,
                    'eventKind' => 'completed',
                    'eventKindLabel' => 'Completed Task',
                    'scheduleLabel' => 'Completed',
                    'lastRunTs' => (int) $history->time,
                    'lastRunIso' => $this->formatClientWallClockIso((int) $history->time),
                    'lastRunLabel' => $historySummary['performedAt'],
                    'nextRunLabel' => 'Not scheduled',
                    'statusLabel' => $historySummary['resultLabel'],
                    'statusTone' => $historySummary['resultTone'],
                    'historySummary' => $historySummary,
                    'historyReportsUrl' => url('histories-reports?display_id=' . $display->id),
                    'schedulerUrl' => url('scheduler?display_id=' . $display->id),
                    'displayCalibrationUrl' => url('display-calibration?display_id=' . $display->id),
                    'locationLabel' => implode(' / ', array_filter($locationBits)),
                ],
            ];
        }

        return $res;
    }
}
