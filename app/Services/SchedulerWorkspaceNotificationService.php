<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\User;
use App\Notifications\WorkspaceNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SchedulerWorkspaceNotificationService
{
    public function syncForUser(User $user): void
    {
        foreach ($this->facilitiesFor($user) as $facility) {
            $stats = $this->getTaskSignalCounts((int) $facility->id);

            $this->syncSignalNotification(
                $user,
                $facility,
                'overdue',
                (int) ($stats['overdue'] ?? 0)
            );

            $this->syncSignalNotification(
                $user,
                $facility,
                'due_today',
                (int) ($stats['dueToday'] ?? 0)
            );
        }
    }

    protected function facilitiesFor(User $user): Collection
    {
        if ($user->role === 'super') {
            return Facility::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        }

        if (!$user->facility_id) {
            return collect();
        }

        $facility = Facility::query()
            ->select(['id', 'name'])
            ->find($user->facility_id);

        return $facility ? collect([$facility]) : collect();
    }

    protected function syncSignalNotification(User $user, Facility $facility, string $kind, int $count): void
    {
        $fingerprint = sprintf('scheduler:%s:facility:%d', $kind, (int) $facility->id);
        $existing = $this->findExistingNotification($user, $fingerprint);

        if ($count <= 0) {
            if ($existing) {
                $existing->delete();
            }

            return;
        }

        $payload = $this->buildPayload($facility, $kind, $count, $fingerprint);

        if ($existing) {
            $data = is_array($existing->data)
                ? $existing->data
                : (json_decode($existing->data ?? '{}', true) ?: []);
            $previousCount = (int) ($data['meta']['count'] ?? 0);
            $shouldResurface = !is_null($existing->read_at) || $previousCount !== $count;

            $existing->data = array_merge($data, $payload);
            if ($shouldResurface) {
                $existing->read_at = null;
                $existing->created_at = now();
            }
            $existing->updated_at = now();
            $existing->save();

            return;
        }

        $user->notify(new WorkspaceNotification($payload));
    }

    protected function buildPayload(Facility $facility, string $kind, int $count, string $fingerprint): array
    {
        $facilityName = $facility->name ?: 'Facility';
        $isOverdue = $kind === 'overdue';
        $verb = $count === 1 ? 'is' : 'are';

        return [
            'category' => 'Task Schedule',
            'title' => $isOverdue ? 'Overdue tasks require attention' : 'Tasks are due today',
            'body' => $count . ' scheduled item' . ($count === 1 ? '' : 's') . ' ' . $verb . ' ' . ($isOverdue ? 'overdue' : 'due today') . ' in ' . $facilityName . '.',
            'severity' => $isOverdue ? 'danger' : 'warning',
            'icon' => 'calendar-clock',
            'url' => route('displays.scheduler'),
            'scope' => $facilityName,
            'meta' => [
                'fingerprint' => $fingerprint,
                'facilityId' => (int) $facility->id,
                'signal' => $kind,
                'count' => $count,
            ],
        ];
    }

    protected function findExistingNotification(User $user, string $fingerprint): ?DatabaseNotification
    {
        $notifications = DatabaseNotification::query()
            ->where('notifiable_id', $user->id)
            ->whereIn('notifiable_type', [
                User::class,
                'App\User',
            ])
            ->where('type', WorkspaceNotification::class)
            ->latest('created_at')
            ->limit(80)
            ->get();

        foreach ($notifications as $notification) {
            $data = is_array($notification->data)
                ? $notification->data
                : (json_decode($notification->data ?? '{}', true) ?: []);

            if (($data['meta']['fingerprint'] ?? null) === $fingerprint) {
                return $notification;
            }
        }

        return null;
    }

    protected function getTaskSignalCounts(int $facilityId): array
    {
        $facilityCond = $facilityId > 0 ? 'f.id=' . $facilityId : '1=1';

        $dueTodayTasks = DB::scalar("
            SELECT COUNT(*) FROM tasks
            INNER JOIN displays ON displays.id = tasks.display_id
            INNER JOIN workstations ON workstations.id = displays.workstation_id
            INNER JOIN workgroups ON workgroups.id = workstations.workgroup_id
            INNER JOIN facilities f ON f.id = workgroups.facility_id
            WHERE {$facilityCond}
              AND tasks.deleted = 0
              AND tasks.nextrun > 0
              AND displays.connected = 1
              AND displays.deleted_at IS NULL
              AND DATE(FROM_UNIXTIME(tasks.nextrun)) = CURRENT_DATE()
        ");

        $overdueTasks = DB::scalar("
            SELECT COUNT(*) FROM tasks
            INNER JOIN displays ON displays.id = tasks.display_id
            INNER JOIN workstations ON workstations.id = displays.workstation_id
            INNER JOIN workgroups ON workgroups.id = workstations.workgroup_id
            INNER JOIN facilities f ON f.id = workgroups.facility_id
            WHERE {$facilityCond}
              AND tasks.deleted = 0
              AND tasks.nextrun > 0
              AND displays.connected = 1
              AND displays.deleted_at IS NULL
              AND DATE(FROM_UNIXTIME(tasks.nextrun)) < CURRENT_DATE()
        ");

        $dueTodayQa = DB::scalar("
            SELECT COUNT(*) FROM qa_tasks
            INNER JOIN displays ON displays.id = qa_tasks.display_id
            INNER JOIN workstations ON workstations.id = displays.workstation_id
            INNER JOIN workgroups ON workgroups.id = workstations.workgroup_id
            INNER JOIN facilities f ON f.id = workgroups.facility_id
            WHERE {$facilityCond}
              AND qa_tasks.deleted_at IS NULL
              AND qa_tasks.nextdate > 0
              AND qa_tasks.nextdate < 4294967295
              AND displays.connected = 1
              AND displays.deleted_at IS NULL
              AND DATE(FROM_UNIXTIME(qa_tasks.nextdate)) = CURRENT_DATE()
        ");

        $overdueQa = DB::scalar("
            SELECT COUNT(*) FROM qa_tasks
            INNER JOIN displays ON displays.id = qa_tasks.display_id
            INNER JOIN workstations ON workstations.id = displays.workstation_id
            INNER JOIN workgroups ON workgroups.id = workstations.workgroup_id
            INNER JOIN facilities f ON f.id = workgroups.facility_id
            WHERE {$facilityCond}
              AND qa_tasks.deleted_at IS NULL
              AND qa_tasks.nextdate > 0
              AND qa_tasks.nextdate < 4294967295
              AND displays.connected = 1
              AND displays.deleted_at IS NULL
              AND DATE(FROM_UNIXTIME(qa_tasks.nextdate)) < CURRENT_DATE()
        ");

        return [
            'dueToday' => (int) $dueTodayTasks + (int) $dueTodayQa,
            'overdue' => (int) $overdueTasks + (int) $overdueQa,
        ];
    }
}
