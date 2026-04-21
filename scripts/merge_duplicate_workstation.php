<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Display;
use App\Models\Workstation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

function fail(string $message, int $code = 1): void
{
    fwrite(STDERR, $message . PHP_EOL);
    exit($code);
}

function latestTimestamp($left, $right)
{
    $leftTime = $left ? strtotime((string) $left) : false;
    $rightTime = $right ? strtotime((string) $right) : false;

    if ($leftTime === false) {
        return $right;
    }

    if ($rightTime === false) {
        return $left;
    }

    return $leftTime >= $rightTime ? $left : $right;
}

function nextLegacyId(string $table): int
{
    return ((int) DB::table($table)->max('id')) + 1;
}

$options = getopt('', ['target:', 'source:', 'backup-dir::', 'dry-run']);

$targetId = isset($options['target']) ? (int) $options['target'] : 0;
$sourceId = isset($options['source']) ? (int) $options['source'] : 0;
$dryRun = array_key_exists('dry-run', $options);

if ($targetId <= 0 || $sourceId <= 0 || $targetId === $sourceId) {
    fail('Usage: php scripts/merge_duplicate_workstation.php --target=<old_ws_id> --source=<new_ws_id> [--backup-dir=<dir>] [--dry-run]');
}

$target = Workstation::with('displays')->find($targetId);
$source = Workstation::with('displays')->find($sourceId);

if (!$target || !$source) {
    fail('Target or source workstation was not found.');
}

$backupDir = $options['backup-dir'] ?? storage_path('backups/workstation-merge-' . Carbon::now()->format('Ymd-His') . "-target{$targetId}-source{$sourceId}");
if (!is_dir($backupDir) && !mkdir($backupDir, 0777, true) && !is_dir($backupDir)) {
    fail("Unable to create backup directory: {$backupDir}");
}

$tablesToBackup = [
    ['table' => 'workstations', 'column' => 'id', 'ids' => [$targetId, $sourceId]],
    ['table' => 'workstation_preferences', 'column' => 'workstation_id', 'ids' => [$targetId, $sourceId]],
    ['table' => 'settings_names', 'column' => 'workstation_id', 'ids' => [$targetId, $sourceId]],
    ['table' => 'displays', 'column' => 'workstation_id', 'ids' => [$targetId, $sourceId]],
];

$displayIds = Display::query()
    ->whereIn('workstation_id', [$targetId, $sourceId])
    ->pluck('id')
    ->map(fn ($id) => (int) $id)
    ->all();

if (!empty($displayIds)) {
    $tablesToBackup = array_merge($tablesToBackup, [
        ['table' => 'display_preferences', 'column' => 'display_id', 'ids' => $displayIds],
        ['table' => 'display_hours', 'column' => 'display_id', 'ids' => $displayIds],
        ['table' => 'tasks', 'column' => 'display_id', 'ids' => $displayIds],
        ['table' => 'qa_tasks', 'column' => 'display_id', 'ids' => $displayIds],
        ['table' => 'histories', 'column' => 'display_id', 'ids' => $displayIds],
        ['table' => 'sync_display_mappings', 'column' => 'resolved_display_id', 'ids' => $displayIds],
        ['table' => 'history_sync_resolutions', 'column' => 'resolved_display_id', 'ids' => $displayIds],
    ]);
}

$tablesToBackup = array_merge($tablesToBackup, [
    ['table' => 'sync_display_mappings', 'column' => 'workstation_id', 'ids' => [$targetId, $sourceId]],
    ['table' => 'history_sync_resolutions', 'column' => 'workstation_id', 'ids' => [$targetId, $sourceId]],
]);

$backupManifest = [];
foreach ($tablesToBackup as $item) {
    $rows = DB::table($item['table'])->whereIn($item['column'], $item['ids'])->get();
    file_put_contents(
        $backupDir . DIRECTORY_SEPARATOR . $item['table'] . '__' . $item['column'] . '.json',
        json_encode($rows, JSON_PRETTY_PRINT)
    );

    $backupManifest[] = [
        'table' => $item['table'],
        'column' => $item['column'],
        'row_count' => $rows->count(),
    ];
}

file_put_contents($backupDir . DIRECTORY_SEPARATOR . 'manifest.json', json_encode([
    'target_workstation_id' => $targetId,
    'source_workstation_id' => $sourceId,
    'dry_run' => $dryRun,
    'created_at' => Carbon::now()->toIso8601String(),
    'tables' => $backupManifest,
], JSON_PRETTY_PRINT));

if ($dryRun) {
    echo json_encode([
        'dry_run' => true,
        'backup_dir' => $backupDir,
        'target' => ['id' => $targetId, 'workstation_key' => $target->workstation_key, 'display_ids' => $target->displays->pluck('id')->all()],
        'source' => ['id' => $sourceId, 'workstation_key' => $source->workstation_key, 'display_ids' => $source->displays->pluck('id')->all()],
    ], JSON_PRETTY_PRINT) . PHP_EOL;
    exit(0);
}

$summary = DB::transaction(function () use ($targetId, $sourceId, $target, $source) {
    $target = Workstation::with('displays')->findOrFail($targetId);
    $source = Workstation::with('displays')->findOrFail($sourceId);

    $movedDisplays = [];
    $deletedDisplays = [];
    $displayIdMap = [];

    foreach ($source->displays as $sourceDisplay) {
        $targetDisplay = $target->displays
            ->first(function ($item) use ($sourceDisplay) {
                $sameSerial = trim((string) $sourceDisplay->serial) !== '' && trim((string) $item->serial) === trim((string) $sourceDisplay->serial);
                $sameClient = (string) $item->client_id === (string) $sourceDisplay->client_id;
                return $sameSerial || $sameClient;
            });

        if (!$targetDisplay) {
            $sourceDisplay->workstation_id = $target->id;
            $sourceDisplay->save();
            $displayIdMap[(int) $sourceDisplay->id] = (int) $sourceDisplay->id;
            $movedDisplays[] = ['mode' => 'reassigned', 'display_id' => $sourceDisplay->id];
            continue;
        }

        $sourceAttrs = $sourceDisplay->getAttributes();
        unset($sourceAttrs['id'], $sourceAttrs['workstation_id'], $sourceAttrs['created_at']);
        foreach ($sourceAttrs as $key => $value) {
            $targetDisplay->{$key} = $value;
        }
        $targetDisplay->workstation_id = $target->id;
        $targetDisplay->save();

        $sourcePreferences = DB::table('display_preferences')->where('display_id', $sourceDisplay->id)->get();
        foreach ($sourcePreferences as $row) {
            $payload = (array) $row;
            $existing = DB::table('display_preferences')
                ->where('display_id', $targetDisplay->id)
                ->where('name', $row->name)
                ->first();

            unset($payload['id']);
            $payload['display_id'] = $targetDisplay->id;

            if ($existing) {
                DB::table('display_preferences')
                    ->where('id', $existing->id)
                    ->update($payload);
            } else {
                $payload['id'] = nextLegacyId('display_preferences');
                DB::table('display_preferences')->insert($payload);
            }
        }

        $sourceHours = DB::table('display_hours')->where('display_id', $sourceDisplay->id)->get();
        foreach ($sourceHours as $row) {
            $payload = (array) $row;
            $existing = DB::table('display_hours')
                ->where('display_id', $targetDisplay->id)
                ->where('start', $row->start)
                ->first();

            unset($payload['id']);
            $payload['display_id'] = $targetDisplay->id;

            if ($existing) {
                DB::table('display_hours')
                    ->where('id', $existing->id)
                    ->update($payload);
            } else {
                $payload['id'] = nextLegacyId('display_hours');
                DB::table('display_hours')->insert($payload);
            }
        }

        DB::table('tasks')->where('display_id', $sourceDisplay->id)->update(['display_id' => $targetDisplay->id]);
        DB::table('qa_tasks')->where('display_id', $sourceDisplay->id)->update(['display_id' => $targetDisplay->id]);
        DB::table('histories')->where('display_id', $sourceDisplay->id)->update(['display_id' => $targetDisplay->id]);
        DB::table('sync_display_mappings')->where('resolved_display_id', $sourceDisplay->id)->update(['resolved_display_id' => $targetDisplay->id]);
        DB::table('history_sync_resolutions')->where('resolved_display_id', $sourceDisplay->id)->update(['resolved_display_id' => $targetDisplay->id]);

        DB::table('display_preferences')->where('display_id', $sourceDisplay->id)->delete();
        DB::table('display_hours')->where('display_id', $sourceDisplay->id)->delete();
        DB::table('displays')->where('id', $sourceDisplay->id)->delete();

        $displayIdMap[(int) $sourceDisplay->id] = (int) $targetDisplay->id;
        $movedDisplays[] = [
            'mode' => 'merged',
            'source_display_id' => $sourceDisplay->id,
            'target_display_id' => $targetDisplay->id,
            'serial' => $targetDisplay->serial,
        ];
        $deletedDisplays[] = $sourceDisplay->id;
    }

    $sourcePrefs = DB::table('workstation_preferences')->where('workstation_id', $sourceId)->get();
    foreach ($sourcePrefs as $row) {
        $payload = (array) $row;
        $payload['workstation_id'] = $targetId;
        $existing = DB::table('workstation_preferences')
            ->where('workstation_id', $targetId)
            ->where('name', $row->name)
            ->first();

        unset($payload['id']);

        if ($existing) {
            DB::table('workstation_preferences')
                ->where('id', $existing->id)
                ->update($payload);
        } else {
            $payload['id'] = nextLegacyId('workstation_preferences');
            DB::table('workstation_preferences')->insert($payload);
        }
    }

    $sourceSettings = DB::table('settings_names')->where('workstation_id', $sourceId)->get();
    foreach ($sourceSettings as $row) {
        $payload = (array) $row;
        $payload['workstation_id'] = $targetId;
        $existing = DB::table('settings_names')
            ->where('workstation_id', $targetId)
            ->where('setting_name', $row->setting_name)
            ->first();

        unset($payload['id']);

        if ($existing) {
            DB::table('settings_names')
                ->where('id', $existing->id)
                ->update($payload);
        } else {
            $payload['id'] = nextLegacyId('settings_names');
            DB::table('settings_names')->insert($payload);
        }
    }

    $sourceMappings = DB::table('sync_display_mappings')->where('workstation_id', $sourceId)->get();
    foreach ($sourceMappings as $row) {
        $resolvedDisplayId = $row->resolved_display_id;
        if ($resolvedDisplayId !== null && isset($displayIdMap[(int) $resolvedDisplayId])) {
            $resolvedDisplayId = $displayIdMap[(int) $resolvedDisplayId];
        }

        $existing = DB::table('sync_display_mappings')
            ->where('workstation_id', $targetId)
            ->where('requested_client_id', $row->requested_client_id)
            ->where('signal_hash', $row->signal_hash)
            ->first();

        if ($existing) {
            DB::table('sync_display_mappings')
                ->where('id', $existing->id)
                ->update([
                    'resolved_display_id' => $resolvedDisplayId ?? $existing->resolved_display_id,
                    'signal_name' => $row->signal_name ?: $existing->signal_name,
                    'signal_regulation' => $row->signal_regulation ?: $existing->signal_regulation,
                    'signal_classification' => $row->signal_classification ?: $existing->signal_classification,
                    'confidence' => $row->confidence ?: $existing->confidence,
                    'hit_count' => (int) $existing->hit_count + (int) $row->hit_count,
                    'last_matched_at' => latestTimestamp($existing->last_matched_at, $row->last_matched_at),
                    'updated_at' => Carbon::now(),
                ]);
        } else {
            $payload = (array) $row;
            unset($payload['id']);
            $payload['workstation_id'] = $targetId;
            $payload['resolved_display_id'] = $resolvedDisplayId;
            DB::table('sync_display_mappings')->insert($payload);
        }
    }
    DB::table('sync_display_mappings')->where('workstation_id', $sourceId)->delete();

    $sourceHistoryResolutions = DB::table('history_sync_resolutions')->where('workstation_id', $sourceId)->get();
    foreach ($sourceHistoryResolutions as $row) {
        $resolvedDisplayId = $row->resolved_display_id;
        if ($resolvedDisplayId !== null && isset($displayIdMap[(int) $resolvedDisplayId])) {
            $resolvedDisplayId = $displayIdMap[(int) $resolvedDisplayId];
        }

        DB::table('history_sync_resolutions')
            ->where('id', $row->id)
            ->update([
                'workstation_id' => $targetId,
                'resolved_display_id' => $resolvedDisplayId,
                'updated_at' => Carbon::now(),
            ]);
    }

    $sourceLastConnected = $source->getRawOriginal('last_connected');
    $targetLastConnected = $target->getRawOriginal('last_connected');

    $target->workstation_key = $source->workstation_key;
    $target->name = $source->name ?: $target->name;
    $target->app = $source->app ?: $target->app;
    $target->ip_address = $source->ip_address ?: $target->ip_address;
    if ($source->workgroup_id) {
        $target->workgroup_id = $source->workgroup_id;
    }
    if ($sourceLastConnected && (!$targetLastConnected || strtotime((string) $sourceLastConnected) >= strtotime((string) $targetLastConnected))) {
        $target->last_connected = $sourceLastConnected;
    }
    $target->save();

    DB::table('workstation_preferences')->where('workstation_id', $sourceId)->delete();
    DB::table('settings_names')->where('workstation_id', $sourceId)->delete();
    DB::table('workstations')->where('id', $sourceId)->delete();

    return [
        'target_workstation_id' => $targetId,
        'source_workstation_id' => $sourceId,
        'target_workstation_key' => $target->workstation_key,
        'moved_displays' => $movedDisplays,
        'deleted_source_display_ids' => $deletedDisplays,
    ];
});

echo json_encode([
    'success' => true,
    'backup_dir' => $backupDir,
    'summary' => $summary,
], JSON_PRETTY_PRINT) . PHP_EOL;
