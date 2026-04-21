<?php

namespace App\Http\Controllers;

use Mail;
use Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Workgroup;
use App\Models\Facility;
use App\Models\Workstation;
use App\Models\Display;
use App\Models\DisplayPreference;
use App\Models\WorkstationPreference;
use App\Models\DisplayHour;
use App\Models\ErrorLimit;
use App\Models\Alert;
use App\Mail\AlertEmail;
use App\Models\User;
use App\Models\Task;
use App\Models\ScheduleType;
use App\Models\TaskType;
use App\Models\SettingsName;
use App\Models\History;
use App\Models\HistorySyncResolution;
use App\Models\QATask;
use App\Models\SyncDisplayMapping;
use DB;
use App\Notifications\DisplayStatusChangedNotification;
use App\Notifications\TaskCompletedNotification;
use App\Notifications\WorkspaceNotification;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Events\TreeChanged;
use App\Models\License;

define('AUTH_FAIL', 1);
define('XML_NOT_VALID', 2);
define('NO_ACTION', 3);
define('OK', 4);
define('UNDEFINED_ACTION', 5);
define('NO_WORKSTATION', 6);
define('NO_WS_GROUP', 7);
define('NO_GROUP', 8);
define('NO_SERVER_CONNECTION', 9);
define('DAMAGED_QRSP_PACKAGE', 10);
define('DELETED', 11);
define('LIMITATION_REACHED', 12);

class Synchronize extends Controller
{
    var $req_data = array();
    var $req_header = array();
    var $res_data = array();
    var $res_header = array('result' => OK);
    var $error_code = OK;
    var $action = '';
    var $facility = null; // Object Facility
    var $workstation = null; // Object Workstation
    var $logger;


    /**
     * Index action
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Get server display from client_display_id
     * 
     * @param  string  $client_display_id
     * @return \App\Display
     */
    private function getDisplay($client_id)
    {
        return $this->workstation->displays()->where('client_id', $client_id)->first();
    }

    private function resolveHistoryDisplay($clientId, array $history = [])
    {
        $display = $this->getDisplay($clientId);
        if ($display) {
            return [
                'display' => $display,
                'resolution' => $this->makeHistoryResolutionMeta('exact', 'high', $clientId, $display, $history, [
                    'matched_client_id' => $display->client_id,
                ]),
            ];
        }

        $fallbackDisplays = $this->workstation->displays()->get();
        $context = $this->buildHistoryResolutionContext($clientId, $history, $fallbackDisplays);

        if ($fallbackDisplays->count() === 1) {
            $fallback = $fallbackDisplays->first();
            $this->logger->info('DEBUG: HISTORY_DISPLAY_FALLBACK ' . json_encode(array_merge($context, [
                'fallback_display_id' => $fallback->id,
                'fallback_client_id' => $fallback->client_id,
                'fallback_serial' => $fallback->serial,
                'fallback_display_name' => $fallback->display_name,
                'fallback_connected' => $fallback->connected,
            ])));

            return [
                'display' => $fallback,
                'resolution' => $this->makeHistoryResolutionMeta(
                    'single_display_fallback',
                    'medium',
                    $clientId,
                    $fallback,
                    $history,
                    ['reason' => 'workstation_has_single_display']
                ),
            ];
        }

        $taskAffinityMatch = $this->resolveHistoryDisplayByTaskAffinity($fallbackDisplays, $history);
        if ($taskAffinityMatch) {
            $this->logger->info('DEBUG: HISTORY_DISPLAY_TASK_AFFINITY_MATCH ' . json_encode(array_merge($context, [
                'matched_display_id' => $taskAffinityMatch['display']->id,
                'matched_client_id' => $taskAffinityMatch['display']->client_id,
                'matched_serial' => $taskAffinityMatch['display']->serial,
                'matched_display_name' => $taskAffinityMatch['display']->display_name,
                'matched_task_type' => $taskAffinityMatch['task_type'],
                'matched_task_ids' => $taskAffinityMatch['task_ids'],
                'score' => $taskAffinityMatch['score'],
            ])));

            return [
                'display' => $taskAffinityMatch['display'],
                'resolution' => $this->makeHistoryResolutionMeta(
                    'task_affinity',
                    $taskAffinityMatch['score'] >= 10 ? 'high' : 'medium',
                    $clientId,
                    $taskAffinityMatch['display'],
                    $history,
                    [
                        'task_type' => $taskAffinityMatch['task_type'],
                        'task_ids' => $taskAffinityMatch['task_ids'],
                        'score' => $taskAffinityMatch['score'],
                    ]
                ),
            ];
        }

        $mapped = $this->resolveHistoryDisplayByStoredMapping($fallbackDisplays, $clientId, $history);
        if ($mapped) {
            $this->logger->info('DEBUG: HISTORY_DISPLAY_MAPPING_MATCH ' . json_encode(array_merge($context, [
                'matched_display_id' => $mapped['display']->id,
                'matched_client_id' => $mapped['display']->client_id,
                'matched_serial' => $mapped['display']->serial,
                'matched_display_name' => $mapped['display']->display_name,
                'mapping_id' => $mapped['mapping']->id,
                'mapping_hit_count' => $mapped['mapping']->hit_count,
                'mapping_confidence' => $mapped['mapping']->confidence,
            ])));

            return [
                'display' => $mapped['display'],
                'resolution' => $this->makeHistoryResolutionMeta(
                    'stored_mapping',
                    $mapped['mapping']->confidence ?: 'medium',
                    $clientId,
                    $mapped['display'],
                    $history,
                    [
                        'mapping_id' => $mapped['mapping']->id,
                        'mapping_hit_count' => $mapped['mapping']->hit_count,
                    ]
                ),
            ];
        }

        $signalMatch = $this->resolveHistoryDisplayByExistingSignal($fallbackDisplays, $history);
        if ($signalMatch) {
            $this->logger->info('DEBUG: HISTORY_DISPLAY_SIGNAL_MATCH ' . json_encode(array_merge($context, [
                'matched_display_id' => $signalMatch['display']->id,
                'matched_client_id' => $signalMatch['display']->client_id,
                'matched_serial' => $signalMatch['display']->serial,
                'matched_display_name' => $signalMatch['display']->display_name,
                'matched_connected' => $signalMatch['display']->connected,
                'score' => $signalMatch['score'],
            ])));

            return [
                'display' => $signalMatch['display'],
                'resolution' => $this->makeHistoryResolutionMeta(
                    'signal_match',
                    $signalMatch['score'] >= 10 ? 'high' : 'medium',
                    $clientId,
                    $signalMatch['display'],
                    $history,
                    ['score' => $signalMatch['score']]
                ),
            ];
        }

        $this->logger->info('DEBUG: HISTORY_DISPLAY_UNRESOLVED ' . json_encode(array_merge($context, [
            'available_displays' => $fallbackDisplays->map(function ($display) {
                return [
                    'id' => $display->id,
                    'client_id' => $display->client_id,
                    'serial' => $display->serial,
                    'display_name' => $display->display_name,
                    'connected' => $display->connected,
                ];
            })->values()->all(),
        ])));

        return [
            'display' => null,
            'resolution' => $this->makeHistoryResolutionMeta(
                'unresolved',
                'low',
                $clientId,
                null,
                $history,
                ['available_display_count' => $fallbackDisplays->count()]
            ),
        ];
    }

    private function buildHistoryResolutionContext($clientId, array $history, $displays)
    {
        return [
            'workstation_id' => $this->workstation?->id,
            'workstation_name' => $this->workstation?->workstation_name,
            'workstation_key' => $this->workstation?->workstation_key,
            'requested_client_id' => (string) $clientId,
            'display_count' => $displays->count(),
            'history_name' => $history['name'] ?? null,
            'history_regulation' => $history['regulation'] ?? null,
            'history_classification' => $history['classification'] ?? null,
            'history_result' => $history['result'] ?? null,
            'history_time' => $history['time'] ?? null,
        ];
    }

    private function normalizeHistorySignalValue($value): string
    {
        return mb_strtolower(trim((string) $value));
    }

    private function buildHistorySignalSignature(array $history): array
    {
        $name = $this->normalizeHistorySignalValue($history['name'] ?? '');
        $regulation = $this->normalizeHistorySignalValue($history['regulation'] ?? '');
        $classification = $this->normalizeHistorySignalValue($history['classification'] ?? '');

        return [
            'name' => $name,
            'regulation' => $regulation,
            'classification' => $classification,
            'hash' => sha1(implode('|', [$name, $regulation, $classification])),
        ];
    }

    private function makeHistoryResolutionMeta(
        string $method,
        string $confidence,
        $clientId,
        ?Display $display,
        array $history,
        array $extra = []
    ): array {
        $context = array_merge([
            'history_name' => $history['name'] ?? null,
            'history_regulation' => $history['regulation'] ?? null,
            'history_classification' => $history['classification'] ?? null,
            'history_result' => $history['result'] ?? null,
            'history_time' => $history['time'] ?? null,
            'workstation_id' => $this->workstation?->id,
            'workstation_name' => $this->workstation?->workstation_name,
            'workstation_key' => $this->workstation?->workstation_key,
        ], $extra);

        $notes = match ($method) {
            'exact' => null,
            'single_display_fallback' => 'Matched by workstation fallback because the client reported a display id that was not present, and this workstation only has one known display.',
            'task_affinity' => 'Matched using a workstation-local scheduled task that strongly fits this history payload.',
            'stored_mapping' => 'Matched using a workstation-local sync mapping learned from previous consistent history patterns.',
            'signal_match' => 'Matched by sync signal because the client reported a non-physical or unmapped display id for this history record.',
            default => 'The client reported a display id that could not be resolved directly.',
        };

        return [
            'method' => $method,
            'confidence' => $confidence,
            'requested_client_id' => (string) $clientId,
            'resolved_display_id' => $display?->id,
            'notes' => $notes,
            'context' => $context,
        ];
    }

    private function resolveHistoryDisplayByStoredMapping($candidateDisplays, $clientId, array $history = [])
    {
        if (!$this->workstation || $candidateDisplays->isEmpty()) {
            return null;
        }

        $signature = $this->buildHistorySignalSignature($history);
        $mapping = SyncDisplayMapping::query()
            ->where('workstation_id', $this->workstation->id)
            ->where('requested_client_id', (string) $clientId)
            ->where('signal_hash', $signature['hash'])
            ->whereIn('resolved_display_id', $candidateDisplays->pluck('id'))
            ->orderByDesc('hit_count')
            ->orderByDesc('last_matched_at')
            ->first();

        if (!$mapping) {
            return null;
        }

        $display = $candidateDisplays->firstWhere('id', $mapping->resolved_display_id);
        if (!$display) {
            return null;
        }

        $mapping->hit_count = (int) $mapping->hit_count + 1;
        $mapping->last_matched_at = now();
        $mapping->save();

        return [
            'display' => $display,
            'mapping' => $mapping,
        ];
    }

    private function resolveHistoryDisplayByTaskAffinity($candidateDisplays, array $history = [])
    {
        if (!$this->workstation || $candidateDisplays->isEmpty()) {
            return null;
        }

        $taskType = $this->inferTaskTypeKeyFromHistory($history);
        if (!$taskType) {
            return null;
        }

        $scored = collect();

        foreach ($candidateDisplays as $candidate) {
            $tasks = Task::query()
                ->where('display_id', $candidate->id)
                ->where('type', $taskType)
                ->orderByDesc('updated_at')
                ->get(['id', 'nextrun', 'lastrun', 'schtype']);

            if ($tasks->isEmpty()) {
                continue;
            }

            $score = 10;
            $score += min(3, $tasks->count());

            if ($tasks->contains(fn ($task) => (int) ($task->lastrun ?? 0) > 0 || (int) ($task->nextrun ?? 0) > 0)) {
                $score += 2;
            }

            $scored->push([
                'display' => $candidate,
                'task_type' => $taskType,
                'task_ids' => $tasks->pluck('id')->values()->all(),
                'score' => $score,
            ]);
        }

        if ($scored->isEmpty()) {
            return null;
        }

        $scored = $scored->sortByDesc('score')->values();
        $top = $scored->first();
        $runnerUp = $scored->get(1);

        if ($runnerUp && $runnerUp['score'] === $top['score']) {
            return null;
        }

        return $top;
    }

    private function inferTaskTypeKeyFromHistory(array $history): ?string
    {
        $haystack = $this->normalizeHistorySignalValue(trim(sprintf(
            '%s %s',
            (string) ($history['name'] ?? ''),
            (string) ($history['regulation'] ?? '')
        )));

        if ($haystack === '') {
            return null;
        }

        $taskTypes = TaskType::query()->get(['key', 'title']);
        $best = null;

        foreach ($taskTypes as $taskType) {
            $needle = $this->normalizeHistorySignalValue($taskType->title ?? '');
            if ($needle === '' || !str_contains($haystack, $needle)) {
                continue;
            }

            $length = mb_strlen($needle);
            if (!$best || $length > $best['length']) {
                $best = [
                    'key' => $taskType->key,
                    'length' => $length,
                ];
            }
        }

        return $best['key'] ?? null;
    }

    private function rememberHistoryDisplayMapping($clientId, Display $display, array $history, array $resolution): void
    {
        if (!$this->workstation) {
            return;
        }

        if (!in_array($resolution['method'] ?? '', ['signal_match', 'stored_mapping'], true)) {
            return;
        }

        $signature = $this->buildHistorySignalSignature($history);

        $mapping = SyncDisplayMapping::firstOrNew([
            'workstation_id' => $this->workstation->id,
            'requested_client_id' => (string) $clientId,
            'signal_hash' => $signature['hash'],
        ]);

        $mapping->signal_name = $signature['name'] !== '' ? $signature['name'] : null;
        $mapping->signal_regulation = $signature['regulation'] !== '' ? $signature['regulation'] : null;
        $mapping->signal_classification = $signature['classification'] !== '' ? $signature['classification'] : null;
        $mapping->resolved_display_id = $display->id;
        $mapping->confidence = $resolution['confidence'] ?? 'medium';
        $mapping->hit_count = $mapping->exists ? ((int) $mapping->hit_count + 1) : 1;
        $mapping->last_matched_at = now();
        $mapping->save();
    }

    private function persistHistoryResolution(History $historyModel, $clientId, ?Display $display, array $resolution): void
    {
        HistorySyncResolution::updateOrCreate(
            ['history_id' => $historyModel->id],
            [
                'workstation_id' => $this->workstation?->id,
                'requested_client_id' => (string) $clientId,
                'resolved_display_id' => $display?->id,
                'method' => $resolution['method'] ?? 'exact',
                'confidence' => $resolution['confidence'] ?? 'high',
                'notes' => $resolution['notes'] ?? null,
                'context' => $resolution['context'] ?? [],
            ]
        );
    }

    private function resolveHistoryDisplayByExistingSignal($candidateDisplays, array $history = [])
    {
        $historyName = trim((string) ($history['name'] ?? ''));
        $historyRegulation = trim((string) ($history['regulation'] ?? ''));
        $historyClassification = trim((string) ($history['classification'] ?? ''));

        $scored = collect();

        foreach ($candidateDisplays as $candidate) {
            $serial = trim((string) ($candidate->serial ?? ''));
            if ($serial === '') {
                continue;
            }

            $peerDisplayIds = Display::query()
                ->where('serial', $serial)
                ->where('id', '!=', $candidate->id)
                ->pluck('id');

            if ($peerDisplayIds->isEmpty()) {
                continue;
            }

            $baseQuery = History::query()->whereIn('display_id', $peerDisplayIds);
            $score = 0;

            if ($historyName !== '' && $historyRegulation !== '' &&
                (clone $baseQuery)->where('name', $historyName)->where('regulation', $historyRegulation)->exists()) {
                $score += 10;
            }

            if ($historyName !== '' &&
                (clone $baseQuery)->where('name', $historyName)->exists()) {
                $score += 4;
            }

            if ($historyRegulation !== '' &&
                (clone $baseQuery)->where('regulation', $historyRegulation)->exists()) {
                $score += 3;
            }

            if ($historyClassification !== '' &&
                (clone $baseQuery)->where('classification', $historyClassification)->exists()) {
                $score += 2;
            }

            if ((clone $baseQuery)->exists()) {
                $score += 1;
            }

            if ($score > 0) {
                $scored->push([
                    'display' => $candidate,
                    'score' => $score,
                    'serial' => $serial,
                ]);
            }
        }

        if ($scored->isEmpty()) {
            return null;
        }

        $scored = $scored->sortByDesc('score')->values();
        $top = $scored->first();
        $runnerUp = $scored->get(1);

        if ($runnerUp && $runnerUp['score'] === $top['score']) {
            $this->logger->info('DEBUG: HISTORY_DISPLAY_SIGNAL_TIE ' . json_encode([
                'workstation_id' => $this->workstation?->id,
                'workstation_key' => $this->workstation?->workstation_key,
                'history_name' => $historyName,
                'history_regulation' => $historyRegulation,
                'history_classification' => $historyClassification,
                'candidates' => $scored->map(function ($item) {
                    return [
                        'display_id' => $item['display']->id,
                        'client_id' => $item['display']->client_id,
                        'serial' => $item['serial'],
                        'score' => $item['score'],
                    ];
                })->all(),
            ]));

            return null;
        }

        return $top;
    }

    private function resolveWorkstationFallback()
    {
        $incomingKey = trim((string) ($this->req_header['workstationid'] ?? ''));
        if ($incomingKey === '' || !$this->facility) {
            return null;
        }

        $workgroupIds = $this->facility->workgroups()->pluck('id');
        if ($workgroupIds->isEmpty()) {
            return null;
        }

        $remoteIp = $_SERVER['REMOTE_ADDR'] ?? null;

        $candidate = Workstation::query()
            ->whereIn('workgroup_id', $workgroupIds)
            ->where(function ($query) {
                $query->whereNull('workstation_key')
                    ->orWhere('workstation_key', '');
            })
            ->when($remoteIp, function ($query) use ($remoteIp) {
                $query->where('ip_address', $remoteIp);
            })
            ->orderByDesc('last_connected')
            ->orderByDesc('updated_at')
            ->first();

        if (!$candidate) {
            return null;
        }

        $candidate->workstation_key = $incomingKey;
        if ($remoteIp) {
            $candidate->ip_address = $remoteIp;
        }
        $candidate->save();

        if ($this->logger) {
            $this->logger->info('DEBUG: WORKSTATION_KEY_BACKFILL ' . json_encode([
                'workstation_id' => $candidate->id,
                'name' => $candidate->name,
                'workstation_key' => $incomingKey,
                'ip_address' => $remoteIp,
            ]));
        }

        return $candidate;
    }

    /**
     * Some client reinstalls generate a brand-new workstation key even though
     * the physical machine name stays the same. When that happens we should
     * adopt the existing workstation row instead of creating a duplicate.
     *
     * To stay conservative, we only auto-adopt when there is a single clear
     * candidate in the same facility by workstation name, or a single exact
     * IP match among multiple same-name candidates.
     */
    private function resolveWorkstationReinstallFallback()
    {
        $incomingKey = trim((string) ($this->req_header['workstationid'] ?? ''));
        $incomingName = trim((string) ($this->req_data['name'] ?? ''));

        if ($incomingKey === '' || $incomingName === '' || !$this->facility) {
            return null;
        }

        $workgroupIds = $this->facility->workgroups()->pluck('id');
        if ($workgroupIds->isEmpty()) {
            return null;
        }

        $remoteIp = $_SERVER['REMOTE_ADDR'] ?? null;
        $incomingApp = trim((string) ($this->req_data['app'] ?? ''));

        $candidates = Workstation::query()
            ->whereIn('workgroup_id', $workgroupIds)
            ->where('name', $incomingName)
            ->where(function ($query) use ($incomingKey) {
                $query->whereNull('workstation_key')
                    ->orWhere('workstation_key', '')
                    ->orWhere('workstation_key', '!=', $incomingKey);
            })
            ->when($incomingApp !== '', function ($query) use ($incomingApp) {
                $query->where(function ($appQuery) use ($incomingApp) {
                    $appQuery->whereNull('app')
                        ->orWhere('app', '')
                        ->orWhere('app', $incomingApp);
                });
            })
            ->orderByDesc('last_connected')
            ->orderByDesc('updated_at')
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        $candidate = null;

        if ($remoteIp) {
            $ipMatches = $candidates->filter(function ($item) use ($remoteIp) {
                return (string) $item->ip_address === (string) $remoteIp;
            })->values();

            if ($ipMatches->count() === 1) {
                $candidate = $ipMatches->first();
            }
        }

        if (!$candidate && $candidates->count() === 1) {
            $candidate = $candidates->first();
        }

        if (!$candidate) {
            if ($this->logger) {
                $this->logger->info('DEBUG: WORKSTATION_REINSTALL_FALLBACK_AMBIGUOUS ' . json_encode([
                    'name' => $incomingName,
                    'workstation_key' => $incomingKey,
                    'ip_address' => $remoteIp,
                    'candidate_ids' => $candidates->pluck('id')->values()->all(),
                    'candidate_keys' => $candidates->pluck('workstation_key')->values()->all(),
                ]));
            }

            return null;
        }

        $oldWorkstationKey = $candidate->workstation_key;

        $candidate->workstation_key = $incomingKey;
        if ($remoteIp) {
            $candidate->ip_address = $remoteIp;
        }
        if ($incomingApp !== '') {
            $candidate->app = $incomingApp;
        }
        $candidate->save();

        if ($this->logger) {
            $this->logger->info('DEBUG: WORKSTATION_REINSTALL_FALLBACK_APPLIED ' . json_encode([
                'workstation_id' => $candidate->id,
                'name' => $candidate->name,
                'old_workstation_key' => $oldWorkstationKey,
                'new_workstation_key' => $incomingKey,
                'ip_address' => $remoteIp,
            ]));
        }

        return $candidate;
    }

    private function normalizeSyncTimestamp($value)
    {
        if ($value === null || $value === '' || $value === 'Never') {
            return null;
        }

        if (!is_numeric($value)) {
            return $value;
        }

        $timestamp = (int) $value;

        // Legacy/sentinel max-int placeholders are not meaningful schedule
        // runtimes and should never be treated as real due dates.
        if (in_array($timestamp, [2147483647, 4294967295], true)) {
            return null;
        }

        return $timestamp;
    }

    private function isUtcMidnightTimestamp($value)
    {
        return is_int($value) && $value > 0 && ($value % 86400) === 0;
    }

    private function qaTaskPreserveWindowSeconds($freq, $freqCodes)
    {
        $haystack = strtolower(trim(sprintf('%s %s', (string) $freq, (string) $freqCodes)));

        return match (true) {
            str_contains($haystack, 'quarter') => 100 * 86400,
            str_contains($haystack, 'month') => 35 * 86400,
            str_contains($haystack, 'week') => 8 * 86400,
            str_contains($haystack, 'day') => 2 * 86400,
            default => 2 * 86400,
        };
    }

    /**
     * Keep QA day-anchor timestamps canonical in UTC so the server never
     * stores mixed precision in nextdate/nextdateFixed for the same task.
     */
    private function canonicalizeQaTaskNextdateFixed(?int $nextdate, ?int $nextdateFixed): ?int
    {
        if (is_int($nextdateFixed) && $this->isUtcMidnightTimestamp($nextdateFixed)) {
            return $nextdateFixed;
        }

        if (!is_int($nextdate) || $nextdate <= 0) {
            return $nextdateFixed;
        }

        return Carbon::createFromTimestampUTC($nextdate)
            ->startOfDay()
            ->timestamp;
    }

    /**
     * When the server still has a pending QA task change (sync=0) but its
     * stored nextdate is only a midnight UTC day-anchor, do not push that
     * stale anchor back to the client if the client is already sending a more
     * precise runtime for the same task.
     */
    private function shouldDeferPendingServerQaOverride($serverTask, array $incomingTask): bool
    {
        if (!$serverTask || (int) ($serverTask->sync ?? 1) !== 0) {
            return false;
        }

        $serverNextdate = $this->normalizeSyncTimestamp($serverTask->getRawOriginal('nextdate'));
        $incomingNextdate = $this->normalizeSyncTimestamp($incomingTask['nextdate'] ?? null);

        if (!is_int($serverNextdate) || !is_int($incomingNextdate)) {
            return false;
        }

        if (!$this->isUtcMidnightTimestamp($serverNextdate)) {
            return false;
        }

        if ($this->isUtcMidnightTimestamp($incomingNextdate)) {
            return false;
        }

        $windowSeconds = $this->qaTaskPreserveWindowSeconds(
            $incomingTask['freq'] ?? $serverTask->freq,
            $incomingTask['freqCodes'] ?? $serverTask->freqCodes
        );

        return abs($incomingNextdate - $serverNextdate) <= $windowSeconds;
    }

    /**
     * Preserve the existing exact next run when the client only sends a
     * day-anchor timestamp at 00:00:00 UTC for a periodic QA task.
     */
    private function shouldPreserveQaTaskExactNextdate($existingTask, $incomingTask)
    {
        if (!$existingTask) {
            return false;
        }

        $incomingNextdate = $this->normalizeSyncTimestamp($incomingTask['nextdate'] ?? null);
        $existingNextdate = $this->normalizeSyncTimestamp($existingTask->getRawOriginal('nextdate'));

        if (!is_int($incomingNextdate) || !is_int($existingNextdate)) {
            return false;
        }

        if (!$this->isUtcMidnightTimestamp($incomingNextdate)) {
            return false;
        }

        if ($this->isUtcMidnightTimestamp($existingNextdate)) {
            return false;
        }

        // If the new timestamp moves forward to a deliberate midnight schedule,
        // let it through. We only protect the case where a precise runtime is
        // collapsed back to a day anchor.
        if ($incomingNextdate > $existingNextdate) {
            return false;
        }

        $windowSeconds = $this->qaTaskPreserveWindowSeconds(
            $incomingTask['freq'] ?? $existingTask->freq,
            $incomingTask['freqCodes'] ?? $existingTask->freqCodes
        );

        return abs($existingNextdate - $incomingNextdate) <= $windowSeconds;
    }

    /**
     * Resolve which QA timestamps should be stored.
     *
     * nextdate:
     * - exact runtime when we have a precise timestamp
     * - preserved runtime when the client only sends a midnight UTC anchor
     *
     * nextdateFixed:
     * - day-anchor sent by client/remote
     * - falls back to nextdate when no explicit anchor exists
     */
    private function resolveQaTaskScheduleTimestamps($existingTask, array $incomingTask): array
    {
        $incomingNextdate = $this->normalizeSyncTimestamp($incomingTask['nextdate'] ?? null);
        $incomingNextdateFixed = $this->normalizeSyncTimestamp($incomingTask['nextdateFixed'] ?? null);

        $resolved = [
            'nextdate' => $incomingNextdate,
            'nextdateFixed' => $incomingNextdateFixed,
            'preserved' => false,
        ];

        if ($this->shouldPreserveQaTaskExactNextdate($existingTask, $incomingTask)) {
            $resolved['nextdate'] = (int) $existingTask->getRawOriginal('nextdate');
            $resolved['nextdateFixed'] = $this->canonicalizeQaTaskNextdateFixed(
                $resolved['nextdate'],
                $incomingNextdateFixed ?? $incomingNextdate
            );
            $resolved['preserved'] = true;
            return $resolved;
        }

        if ($resolved['nextdateFixed'] === null && $this->isUtcMidnightTimestamp($incomingNextdate)) {
            $resolved['nextdateFixed'] = $incomingNextdate;
        }

        $resolved['nextdateFixed'] = $this->canonicalizeQaTaskNextdateFixed(
            $resolved['nextdate'],
            $resolved['nextdateFixed']
        );

        return $resolved;
    }

    private function resolveCalibrationTaskNextRun(Display $display, array $task): ?int
    {
        if (($task['deleted'] ?? 0) == 1) {
            return null;
        }

        $scheduleType = (string) ($task['schtype'] ?? '');
        if ($scheduleType === '' || $scheduleType === '0') {
            return null;
        }

        $startDate = trim((string) ($task['startdate'] ?? ''));
        $startTime = trim((string) ($task['starttime'] ?? ''));
        if ($startDate === '' || $startTime === '') {
            return null;
        }

        $normalizedDate = str_replace('.', '-', $startDate);
        $normalizedTime = substr($startTime, 0, 5);
        $timezone = optional(optional(optional($display->workstation)->workgroup)->facility)->timezone
            ?: config('app.timezone', 'UTC');

        try {
            return Carbon::createFromFormat('Y-m-d H:i', "{$normalizedDate} {$normalizedTime}", $timezone)
                ->utc()
                ->timestamp;
        } catch (\Throwable $e) {
            $this->logger->info('DEBUG: CALTASK_NEXTRUN_RESOLVE_FAILED ' . json_encode([
                'display_id' => $display->id,
                'client_display_id' => $display->client_id,
                'startdate' => $startDate,
                'starttime' => $startTime,
                'timezone' => $timezone,
                'message' => $e->getMessage(),
            ]));

            return null;
        }
    }

    private function shouldCloseCompletedOneShotCalibrationTask(array $task, ?Task $existingTask = null): bool
    {
        $scheduleType = (int) ($task['schtype'] ?? ($existingTask->schtype ?? -1));
        if (!in_array($scheduleType, [ScheduleType::STARTUP, ScheduleType::ONCE], true)) {
            return false;
        }

        $incomingLastRun = (int) ($task['lastrun'] ?? 0);
        $existingLastRun = (int) ($existingTask->lastrun ?? 0);

        return max($incomingLastRun, $existingLastRun) > 0;
    }

    private function resolveHistoryTaskTypeKeys(array $history): array
    {
        $terms = collect([
            trim((string) ($history['regulation'] ?? '')),
            trim((string) ($history['name'] ?? '')),
            trim((string) ($history['classification'] ?? '')),
        ])->filter()->unique()->values();

        if ($terms->isEmpty()) {
            return [];
        }

        $taskTypes = TaskType::query()->get(['key', 'title']);
        $matches = $taskTypes->filter(function ($taskType) use ($terms) {
            $title = strtolower(trim((string) ($taskType->title ?? '')));
            $key = strtolower(trim((string) ($taskType->key ?? '')));

            foreach ($terms as $term) {
                $needle = strtolower(trim((string) $term));
                if ($needle === '') {
                    continue;
                }

                if ($needle === $title || $needle === $key) {
                    return true;
                }

                if ($title !== '' && str_contains($needle, $title)) {
                    return true;
                }

                if ($title !== '' && str_contains($title, $needle)) {
                    return true;
                }
            }

            return false;
        });

        return $matches
            ->pluck('key')
            ->filter(fn ($value) => trim((string) $value) !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function syncCalibrationTaskRunFromHistory(Display $display, array $history): void
    {
        $result = (int) ($history['result'] ?? 0);
        $runTimestamp = (int) ($history['time'] ?? 0);
        if (!in_array($result, [2, 3], true) || $runTimestamp <= 0) {
            return;
        }

        $taskTypeKeys = $this->resolveHistoryTaskTypeKeys($history);
        if (empty($taskTypeKeys)) {
            $this->logger->info('DEBUG: HISTORY_TASK_SYNC_SKIPPED_NO_MATCH ' . json_encode([
                'display_id' => $display->id,
                'history_name' => $history['name'] ?? null,
                'history_regulation' => $history['regulation'] ?? null,
            ]));
            return;
        }

        $task = Task::query()
            ->where('display_id', $display->id)
            ->where('deleted', 0)
            ->whereIn('type', $taskTypeKeys)
            ->where(function ($query) use ($runTimestamp) {
                $query->whereNull('lastrun')
                    ->orWhere('lastrun', '<', $runTimestamp);
            })
            ->orderByRaw('CASE WHEN nextrun > 0 AND nextrun <= ? THEN 0 ELSE 1 END', [$runTimestamp])
            ->orderByRaw('CASE WHEN nextrun > 0 THEN ABS(nextrun - ?) ELSE 2147483647 END', [$runTimestamp])
            ->orderByDesc('id')
            ->first();

        if (!$task) {
            $this->logger->info('DEBUG: HISTORY_TASK_SYNC_SKIPPED_NO_TASK ' . json_encode([
                'display_id' => $display->id,
                'task_type_keys' => $taskTypeKeys,
                'history_name' => $history['name'] ?? null,
                'history_regulation' => $history['regulation'] ?? null,
            ]));
            return;
        }

        $before = [
            'task_id' => $task->id,
            'task_type' => $task->type,
            'previous_lastrun' => $task->lastrun,
            'previous_nextrun' => $task->nextrun,
        ];

        // History already comes from the client runtime, so we should only
        // mirror the latest execution timestamp here. Re-triggering the task
        // observer from this path can corrupt nextrun for periodic tasks when
        // the row also carries future schedule metadata from the client.
        $task->lastrun = $runTimestamp;
        if (in_array((int) ($task->schtype ?? 0), [ScheduleType::STARTUP, ScheduleType::ONCE], true)) {
            $task->nextrun = 0;
        }
        $task->sync = 1;
        $task->updated_at = now();
        $task->save();

        $this->logger->info('DEBUG: HISTORY_TASK_SYNC_APPLIED ' . json_encode(array_merge($before, [
            'new_lastrun' => $task->lastrun,
            'new_nextrun' => $task->nextrun,
            'history_time' => $runTimestamp,
            'history_name' => $history['name'] ?? null,
            'history_regulation' => $history['regulation'] ?? null,
        ])));
    }

    /**
     * Main processing 
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function action(Request $request)
    {
        // we can't use $request->all because SETTINGSNAMES sending UTF8 codes
        $json = json_decode(utf8_encode($request->getContent()), true);


        // Get request data and header
        $this->req_header = $json['header'];
        $this->req_data = $json['data'];

        // init logger
        $this->logger = new Logger('sync');
        $filename = sprintf('%s/logs/sync_%s_%s.log', storage_path(), $this->req_header['login'], date('Ymd'));
        $this->logger->pushHandler(new StreamHandler($filename, Logger::INFO));

        //Log::useDailyFiles(storage_path().'/logs/sync_'.$this->req_header['login'].'.log', 'info');
        $this->logger->info('>>: ' . json_encode($json));
        //Log::info('CLIENT TO SERVER: '.json_encode($json,JSON_PRETTY_PRINT));

        // Check for user authetication (from header login and password parameters)
        if (!$this->checkLoginAuth()) {
            return $this->getResponse(AUTH_FAIL);
        }

        // Check workstation 
        $this->workstation = Workstation::whereWorkstation_key($this->req_header['workstationid'])->first();
        if (!$this->workstation) {
            $this->workstation = $this->resolveWorkstationFallback();
        }

        // Call the action method
        $this->action = strtoupper($this->req_header['action']);
        if (!method_exists($this, $this->action)) {
            return $this->getResponse(NO_ACTION);
        } else {
            // wrap the action into transaction to make sure the consistent of DB
            // DO NOT CALL ACTION IF THERE IS NO DATA
            //if (count($this->req_data) > 0) {
            DB::transaction(function () {
                $res = call_user_func(array($this, $this->action));
            });
            //}
        }

        // Send response back to client
        return $this->getResponse();
    }

    /**
     * Get JSON response to send to client
     * 
     * @return String
     */
    private function getResponse($error_code = 0)
    {
        // If pass error_code then set it to global variable 
        if ($error_code > 0) {
            $this->error_code = $error_code;
        }

        $this->res_header = array('result' => $this->error_code);

        // Result failed, then clear data
        if ($this->error_code !== OK) {
            $this->res_data = array();
        }

        // Result OK
        // wrap the result into JSON 
        $res = array(
            'header' => $this->res_header,
            'data' => $this->res_data
        );

        //Log::useDailyFiles(storage_path().'/logs/sync_'.$this->req_header['login'].'.log', 'info');                    
        $this->logger->info('<<: ' . json_encode($res));
        //Log::info('SERVER TO CLIENT: '.json_encode($res,JSON_PRETTY_PRINT));
        return response()->json($res, 200);
    }

    /**
     * Check for user login authentication
     * Get the current facility of the user
     * 
     * @return Boolean
     */
    private function checkLoginAuth()
    {
        $user = User::where([
            'sync_user' => $this->req_header['login'],
            'sync_password' => $this->req_header['pwd'],
            'status' => 1,
            'enabled' => 1
        ])->first();

        if (!$user) {
            return false;
        }

        $this->facility = $user->facility;

        return true;
    }

    /**
     * Return the group list of the user's facility
     * Then add new workstation
     * 
     * @return Boolean
     */
    private function GROUPS()
    {
        // Get list of workgroups under the current facility, only get name and id fields
        $wg = $this->facility->workgroups->map(function ($item) {
            return ['name' => $item['name'], 'id' => $item['id']];
        })->toArray();

        // If workstation does not exist, then add new workstation

        if (!$this->workstation) {
            $this->workstation = $this->resolveWorkstationReinstallFallback();
        }

        if (!$this->workstation) {
            // check connection limitation
            $license = License::find(1);
            // $maxconn = hexdec(substr($license->activation_code, 10, 2));
            $maxconn = ($license->max_connections);
            $numconn = Workstation::whereNull('deleted_at')->count();
            $this->logger->info('<<: maxconn=' . $maxconn . ' - numconn=' . $numconn);
            if ($maxconn < $numconn) {
                return $this->getResponse(LIMITATION_REACHED);
            }


            $this->workstation = Workstation::create(
                [
                    'workstation_key' => $this->req_header['workstationid'],
                    'name' => $this->req_data['name'],
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'app' => $this->req_data['app'],
                    //'app_version' => $this->req_data['appversion']
                ]
            );
        } else {
            if ($this->workstation->name != $this->req_data['name']) {
                $this->workstation->name = $this->req_data['name'];
                $this->workstation->app = $this->req_data['app'];
                //$this->workstation->app_version = $this->req_data['appversion'];
                $this->workstation->save();

                event(new TreeChanged($this->facility->id));
            }
        }

        // return list of workgroups        
        $this->res_data = $wg;

        return true;
    }

    /**
     * Connect the workstation to the selected group
     * 
     * @return Boolean
     */
    private function SELECTGROUP()
    {
        // Check if workgroup id exist
        if (!Workgroup::find($this->req_data['id'])) {
            $this->error_code = NO_WS_GROUP;
            return false;
        }
        // update the workgroup id
        $this->workstation->workgroup_id = $this->req_data['id'];
        $this->workstation->save();

        return true;
    }

    /**
     * Synchronize displays
     * 
     * @return Boolean
     */
    private function DISPLAY()
    {
        $this->logger->info('DEBUG:workstation-id: ' . $this->workstation->id);
        // firstly we disconnect all displays, then connect the ones that sent from client
        Display::whereWorkstation_id($this->workstation->id)->update(['connected' => false]);
        // update or create displays under $this->workstation
        foreach ($this->req_data as $data) {
            $d = Display::where(['client_id' => $data['id'], 'workstation_id' => $this->workstation->id])->first();
            if (!$d) $d = new Display();

            $old = $d->serial;
            // remap
            //$data['active'] = $data['isactive'];

            $data['client_id'] = $data['id'];
            $data['connected'] = true;
            $data['workstation_id'] = $this->workstation->id;
            $data['sync'] = 1;
            $data['app'] = $this->workstation->app;
            //$data['app_version'] = $this->workstation->appversion;
            unset($data['isactive']);
            unset($data['id']);

            foreach ($data as $name => $value) {
                $d->$name = $value;
            }
            $d->save();

            if ($old != $d->serial) {
                event(new TreeChanged($this->facility->id));
            }
        }

        // Some client builds only call DISPLAY/SETTINGSNAMES during remote sync,
        // so treat this as a valid heartbeat for workstation connectivity.
        $this->workstation->last_connected = Carbon::now();
        $this->workstation->save();

        return true;
    }

    /**
     * Update display preferences
     * Then send the preferences that changed on server to client
     * If client and server has changes at the same time, change on server is higher priority
     * 
     * @return Boolean
     */
    private function DISPLAYPREFS()
    {

        foreach ($this->req_data as $client_id => $prefs) {
            // Find the display_id by client_id
            $display = $this->getDisplay($client_id);
            if (!$display) continue;
            
            if (!is_numeric($display->id)) {
                $this->logger->error("Invalid display_id: " . $display->id);
            }
        
            // Get changes on server to send to client
            $serverChanges = DisplayPreference::where(['display_id' => $display->id, 'sync' => 0])->get();
            foreach ($serverChanges as $p) {
                $this->res_data[$client_id][$p['name']] = $p['value'];
                // unset p['name'] so it does not update value from client
                unset($prefs[$p['name']]);
            }

            // update visible to 0
            $this->logger->error("Preferences display_id: " . $display->id);
            DisplayPreference::where(['display_id' => $display->id])->update(['visible' => 0]);
            $this->logger->error("Preferences display_id AFTER: " . $display->id);

            // Update data from client
            foreach ($prefs as $name => $value) {
                $update_data = array('value' => $value, 'visible' => 1);
                // prefer server change: only update if server did not change 
                $where = array('display_id' => $display->id, 'name' => $name);
                $this->logger->error("Preferences update where: " . json_encode($where, 1));
                $this->logger->error("Preferences update data: " . json_encode($update_data, 1));
                DisplayPreference::updateOrCreate($where, $update_data);
            }

            // update server changes as synced
            DisplayPreference::where(['display_id' => $display->id, 'sync' => 0])->update(['sync' => 1]);
        }

        return true;
    }


    /**
     * Update the workstation preferences from client
     * Then send the preferences that changed on server to client
     * If client and server has changes at the same time, change on server is higher priority
     * 
     * @return Boolean
     */
    private function PREFERENCES()
    {
        // Get server changes to send to client
        $serverChanges = $this->workstation->preferences()->where('sync', 0)->get();

        foreach ($serverChanges as $p) {
            $this->res_data[$p->name] = $p->value;
            // unset p['name'] so it does not update value from client
            unset($this->req_data[$p->name]);
            $p->sync = 1;
            $p->save();
        }


        foreach ($this->req_data as $name => $value) {
            $update_data = array('value' => $value, 'sync' => 1);
            $where = array('workstation_id' => $this->workstation->id, 'name' => $name);
            WorkstationPreference::updateOrCreate($where, $update_data);
        }

        // update workstation.last_connected
        $this->workstation->last_connected = Carbon::now();
        $this->workstation->save();

        return true;
    }

    /**
     * Add Display working hours
     * 
     * @return Boolean
     */
    private function DISPLAYHOURS()
    {

        foreach ($this->req_data as $client_id => $hours) {
            $display = $this->getDisplay($client_id);
            if (!$display) continue;
            foreach ($hours as $hour) {
                $start = Carbon::createFromFormat('Y.m.d h:i:s', $hour['date'] . '00:00:00');
                $end = clone $start;
                $end->addHours($hour['hours']);


                $update_data = array('duration' => $hour['hours'], 'end' => $end);
                $where = array('start' => $start, 'display_id' => $display->id);
                DisplayHour::updateOrCreate($where, $update_data);
            }
        }

        return true;
    }

    /**
     * Update display statuses
     * 
     * @return Boolean
     */
    private function DISPLAYSTATUSES()
    {
        foreach ($this->req_data as $client_id => $statuses) {
            $changed = false;
            // Get display id
            $display = $this->getDisplay($client_id);
            if (!$display) continue;

            $oldStatus = $display->status;
            $newStatus = Display::STATUS_OK;
            $errors = [];
            // Compare with error limits values
            foreach ($statuses as $name => $value) {
                // Find the error limit
                $limit = ErrorLimit::find($name);
                // Compare $value with the limit value
                if (!$limit->eval($value)) {
                    $newStatus = Display::STATUS_FAILED;
                    if ($name == 'all_qa_steps_ok') {
                        $msg = 'QA Steps Not OK';
                    } else {
                        $msg = $limit->name . ' Error: ' . $value . $limit->ioperator . $limit->value;
                    }
                    $errors[] = $msg;
                }
            }
            $errors = json_encode($errors);
            if ($newStatus != $oldStatus) {
                $display->status = $newStatus;
                $changed = true;
            }

            if ($display->errors != $errors) {
                $display->errors = $errors;
                $changed = true;
            }

            // If status changed then send alert emails
            // - If status = FAIL then send fail alert emails
            // - If status = OK and old_status = fail -> send OK emails
            // if (
            //     $changed
            //     || ($newStatus == Display::STATUS_OK && $oldStatus == Display::STATUS_FAILED)
            // ) {
            //     //$alerts = Alert::whereRaw('alert_status | '.$newStatus . ' = ' . $newStatus)->get();
            //     $alerts = Alert::where([['facility_id', $this->facility->id], ['actived', 1]])->get();

            //     foreach ($alerts as $alert) {
            //         if (!config('app.offline')) {
            //             $alert->notify(new DisplayStatusNotification($display, $oldStatus, $newStatus));
            //         }
            //     }
            // }



            if ($changed) {
                $display->save();
            }

        }

        return true;
    }

    /**
     * Calibration Tasks synchronization
     * 
     * @return Bosolean
     */


    private function CALTASKS()
    {

        // Get changes from servers
        $serverChanges = $this->workstation->tasks()->where('tasks.sync', 0)->get();

        $excludeClients = [];
        $excludeServers = [];
        foreach ($serverChanges as $task) {
            $res = [
                'displayId' => $task->display->client_id,
                'deleted' => $task->deleted ? 1 : 0,
                'testpattern' => $task->testpattern,
                'schtype' => $task->schtype,
                'startdate' => $task->startdate,
                'starttime' => $task->starttime,
                'status' => $task->status,
                'type' => $task->type,
                'weekofmonth' => $task->weekofmonth,
                'nthflag' => $task->nthflag,
                'nextrun' => $task->nextrun,
                'lastrun' => $task->lastrun,
                'monthes' => $task->monthes,
                'everynweek' => $task->everynweek,
                'everynday' => $task->everynday,
                'daysofweek' => $task->daysofweek,
                'dayofmonth' => $task->dayofmonth,
            ];
            // client task
            if ($task->client_id) {
                $res['taskId'] = $task->client_id;
                $excludeClients[] = $task->client_id;
                $this->res_data['tasks'][] = $res;
            } else { // server task
                $res['serverTaskId'] = $task->id;
                $excludeServers[] = $task->id;
                $this->res_data['tasks'][] = $res;
            }
            $task->sync = 1;
            $task->save();
        }
        if (isset($this->req_data['tasks'])) {
            foreach ($this->req_data['tasks'] as $task) {

                $display = $this->getDisplay($task['displayId']);
                if (!$display) continue;

                $isServerTask = isset($task['serverTaskId']) && $task['serverTaskId'] != '';
                if (isset($task['serverTaskId']) && $task['serverTaskId'] != '') { // server task
                    // Do not update if server changed
                    if (in_array($task['serverTaskId'], $excludeServers)) {
                        continue;
                    }
                    $where = array('id' => $task['serverTaskId'], 'display_id' => $display->id);
                } else { // client task
                    // Do not update if server changed
                    if (in_array($task['taskId'], $excludeClients)) {
                        continue;
                    }
                    $where = array('client_id' => $task['taskId'], 'display_id' => $display->id);
                }

                $existingTask = Task::where($where)->first();

                if (!$isServerTask && !$existingTask && !$this->isCompleteClientCalibrationTaskPayload($task)) {
                    $this->logger->info('DEBUG: SKIP_INCOMPLETE_CLIENT_CALTASK ' . json_encode([
                        'display_id' => $display->id,
                        'client_task_id' => $task['taskId'] ?? null,
                        'payload_keys' => array_keys($task),
                    ]));
                    continue;
                }

                // IMPORTANT - reset update_data for each task
                $update_data = [];
                // update data
                foreach ($task as $k => $v) {
                    $update_data[$k] = $v;
                }
                // special case handle
                $update_data['sync'] = 1;
                $timestamp = Carbon::now()->toDateTimeString();
                if (!$existingTask) {
                    $update_data['created_at'] = $timestamp;
                }
                $update_data['updated_at'] = $timestamp;

                $resolvedNextRun = $this->resolveCalibrationTaskNextRun($display, $task);
                if ($resolvedNextRun !== null) {
                    $update_data['nextrun'] = $resolvedNextRun;
                }

                if ($this->shouldCloseCompletedOneShotCalibrationTask($task, $existingTask)) {
                    $update_data['nextrun'] = 0;
                }

                Task::updateOrCreate($where, $update_data);
            }
        }

        // Delete all tasks that have deleted=1 and sync=1
        // use toBase to prevent ambiguous column updated_at
        $this->workstation->tasks()->where(['deleted' => 1, 'tasks.sync' => 1])->toBase()->delete();
    }

    private function isCompleteClientCalibrationTaskPayload(array $task): bool
    {
        if (($task['deleted'] ?? 0) == 1) {
            return true;
        }

        $required = ['type', 'schtype'];
        foreach ($required as $field) {
            if (!array_key_exists($field, $task) || $task[$field] === '' || $task[$field] === null) {
                return false;
            }
        }

        $scheduleType = (string) ($task['schtype'] ?? '');
        if ($scheduleType === '0') {
            return true;
        }

        return !empty($task['startdate']) && !empty($task['starttime']);
    }

    private function QATASKS()
    {
        $incomingTaskIndex = collect((array) $this->req_data)
            ->filter(fn ($task) => is_array($task) && !empty($task['taskKey']) && array_key_exists('displayid', $task))
            ->keyBy(function ($task) {
                return trim((string) ($task['taskKey'] ?? '')) . '|' . trim((string) ($task['displayid'] ?? ''));
            });

        // remove qatasks not exists on client
        $serverTasks = $this->workstation->qatasks()->get();
        // get array of taskkey from client
        $clientTasks = array_reduce(
            $this->req_data,
            function ($carry, $item) {
                $carry[] = $item['taskKey'];
                return $carry;
            },
            []
        );
        
        $excludeServers = [];
        $serverTasks = $serverTasks->filter(function ($task, $key) use ($clientTasks, &$excludeServers, $incomingTaskIndex) {
            if (!in_array($task->taskKey, $clientTasks)) {
                $this->logger->info('DEBUG: should delete' . json_encode($task->taskKey));
                $task->delete();
                return false;
            }
            // filter server changes to sync back to client
            if ($task->sync == 0) {
                $incomingKey = trim((string) $task->taskKey) . '|' . trim((string) optional($task->display)->client_id);
                $incomingTask = $incomingTaskIndex->get($incomingKey);

                if ($incomingTask && $this->shouldDeferPendingServerQaOverride($task, $incomingTask)) {
                    $this->logger->info('DEBUG: DEFER_STALE_SERVER_QATASK_OVERRIDE' . json_encode([
                        'taskKey' => $task->taskKey,
                        'display_id' => $task->display_id,
                        'server_nextdate' => $task->getRawOriginal('nextdate'),
                        'incoming_nextdate' => $this->normalizeSyncTimestamp($incomingTask['nextdate'] ?? null),
                    ]));

                    return true;
                }

                $res = [
                    'displayid' => strval($task->display->client_id),
                    'nextdate' => $task->nextdate_timestamp,
                    'taskKey' => $task->taskKey,
                ];
                $excludeServers[] = $task->taskKey;
                $this->res_data[] = $res;
                $task->sync = 1;
                $task->save();
            }

            return true;
        });

        $this->logger->info('DEBUG: excludeServers' . json_encode($excludeServers));
        $this->logger->info('DEBUG: res_data' . json_encode($this->res_data));
        if (isset($this->req_data)) {
            foreach ($this->req_data as $task) {

                $display = $this->getDisplay($task['displayid']);
                if (!$display) continue;

                // Do not update if server changed
                if (in_array($task['taskKey'], $excludeServers)) {
                    $this->logger->info('IGNORE' . $task['taskKey']);
                    continue;
                }
                $where = ['taskKey' => $task['taskKey'], 'display_id' => $display->id];
                $existingTask = QATask::where($where)->first();

                $resolvedSchedule = $this->resolveQaTaskScheduleTimestamps($existingTask, $task);
                $incomingNextdate = $resolvedSchedule['nextdate'];
                $incomingNextdateFixed = $resolvedSchedule['nextdateFixed'];

                // IMPORTANT - reset update_data for each task
                $update_data = [
                    'name' => $task['name'],
                    'freq' => $task['freq'],
                    'freqCodes' => $task['freqCodes'],
                    'lastrundate' => $task['lastrundate'],
                    'nextdate' => $incomingNextdate,
                    'nextdateFixed' => $incomingNextdateFixed,
                    'taskStatus' => $task['taskStatus'],
                    'exceptions' => json_encode($task['exceptions']),
                    'stepsIds' => json_encode($task['stepsIds'])
                ];

                if ($resolvedSchedule['preserved']) {
                    $this->logger->info('DEBUG: PRESERVE_QATASK_NEXTDATE' . json_encode([
                        'taskKey' => $task['taskKey'],
                        'display_id' => $display->id,
                        'freq' => $task['freq'] ?? null,
                        'incoming_nextdate' => $this->normalizeSyncTimestamp($task['nextdate'] ?? null),
                        'incoming_nextdateFixed' => $incomingNextdateFixed,
                        'preserved_nextdate' => $incomingNextdate,
                    ]));
                }

                // special case handle
                $update_data['sync'] = 1;
                $this->logger->info('DEBUG: UPDATE DATA' . json_encode($update_data));
                QATask::updateOrCreate($where, $update_data);
            }
        }

    }

    private function QASTEPS()
    {
        if (!$this->workstation) {
            return;
        }

        \App\Models\WorkstationPreference::updateOrCreate(
            [
                'name' => 'QA_steps_catalog',
                'workstation_id' => $this->workstation->id,
            ],
            [
                'value' => json_encode([
                    'received_at' => Carbon::now()->toIso8601String(),
                    'steps' => $this->req_data,
                ], JSON_UNESCAPED_UNICODE),
                // Client-owned read-only catalog; do not push it back as a pending preference.
                'sync' => 1,
            ]
        );
    }

    /**
     * Update the changed preferences from clientend
     * Then send the preferences that changed on server to client
     * If client and server has changes at the same time, change on server is higher priority
     * 
     * @return Bosolean
     */
    private function QATASKS_OLD()
    {

        foreach ($this->req_data as $task) {
            $display = $this->getDisplay($task['displayid']);
            if (!$display) continue;

            // delete old data before adding new ones
            //QATask::where(['display_id' => $display->id])->delete();
            $update_data = [
                'name' => $task['name'],
                'freq' => $task['freq'],
                'freqCodes' => $task['freqCodes'],
                'lastrundate' => $task['lastrundate'],
                'nextdate' => $task['nextdate'],
                'nextdateFixed' => $task['nextdateFixed'],
                'taskStatus' => $task['taskStatus'],
                'exceptions' => json_encode($task['exceptions']),
                'stepsIds' => json_encode($task['stepsIds'])
            ];
            $where = ['taskKey' => $task['taskKey'], 'display_id' => $display->id];
            QATask::updateOrCreate($where, $update_data);
        }
    }

    /**
     * Update the settings names
     * 
     * @return Bosolean
     */
    private function SETTINGSNAMES()
    {
        foreach ($this->req_data as $setting_name => $setting_value) {
            // parse value

            $arr_value = array();
            $arr = explode('|', $setting_value);
            for ($i = 0; $i < count($arr) / 2; $i++) {
                // Ensure both $arr[$i * 2] and $arr[$i * 2 + 1] exist
                if (isset($arr[$i * 2]) && isset($arr[$i * 2 + 1])) {
                    $value = $arr[$i * 2];
                    $name = $arr[$i * 2 + 1];
                    $arr_value[$name] = utf8_decode($value);
                } else {
                    // Log or handle the invalid structure if needed
                    $this->logger->error("Invalid setting value format: " . $setting_value);
                    continue;
                }
                /*$value = $arr[$i * 2];
                $name = $arr[$i * 2 + 1];
                //$arr_value[$name] = utf8_decode($value);
                $arr_value[$name] = utf8_decode($value);*/
            }
            // put JSON_UNESCAPED_UNICODE option to fix utf8 problem
            $update_data = array(
                'setting_value' => json_encode($arr_value, JSON_UNESCAPED_UNICODE)
            );
            $where = array('setting_name' => $setting_name, 'workstation_id' => $this->workstation->id);
            //$this->logger->error("Settings update where: " . json_encode($where, 1));
            //$this->logger->error("Settings update data: " . json_encode($update_data, 1));
            SettingsName::updateOrCreate($where, $update_data);
        }

        // Older/limited clients may never call PREFERENCES, so keep the
        // workstation heartbeat fresh when SETTINGSNAMES arrives.
        $this->workstation->last_connected = Carbon::now();
        $this->workstation->save();
    }

    /**
     * Update the history
     * 
     * @return Bosolean
     */
    private function HISTORY()
    {
        $results = [];
        foreach ($this->req_data as $history) {
            $client_id = $history['displayid'];
            // get the server display 
            $resolved = $this->resolveHistoryDisplay($client_id, $history);
            $display = $resolved['display'] ?? null;
            $resolution = $resolved['resolution'] ?? [];
            if (!$display) continue;
            
            if(!isset($history['regulation'])) $history['regulation']='';
            if(!isset($history['classification'])) $history['classification']='';

            $update_data = [
                'type' => $history['type'],
                'result' => $history['result'],
                'name' => $history['name'],
                'isdisabled' => $history['isdisabled'],
                'header' => isset($history['header']) ? json_encode($history['header']) : '{}',
                'steps' => isset($history['steps']) ? json_encode($history['steps']) : '{}',
                'levels' => isset($history['levels']) ? json_encode($history['levels']) : '{}',
                'measurements' => isset($history['measurements']) ? json_encode($history['measurements']) : '{}',
                'regulation' => $history['regulation'],
                'classification' => $history['classification'],
                'scores' => isset($history['scores']) ? json_encode($history['scores']) : '{}'

            ];


            $where = ['display_id' => $display->id, 'time' => $history['time']];
            
            $newHist = History::where($where)->first();
            if (!$newHist) {
                $update_data['id'] = History::max('id') + 1;
                $newHist = History::create(array_merge($where, $update_data));
            } else {
                $newHist->update($update_data);
            }

            $this->persistHistoryResolution($newHist, $client_id, $display, $resolution);
            $this->rememberHistoryDisplayMapping($client_id, $display, $history, $resolution);

            $this->syncCalibrationTaskRunFromHistory($display, $history);
            
            // store every time to get the latest history after all
            $results[$display->id] = $newHist;

            // Send task completed email with PDF report for OK and Failed tasks
            if (in_array($history['result'], [2, 3]) && !config('app.offline')) {
                try {
                    $alerts = Alert::where([['facility_id', $this->facility->id], ['actived', 1]])->with('user')->get();
                    foreach ($alerts as $alert) {
                        $alert->notify(new TaskCompletedNotification($newHist));
                    }

                    $alerts->pluck('user')
                        ->filter()
                        ->unique('id')
                        ->each(function ($user) use ($newHist) {
                            $resultFailed = (int) $newHist->result === 3;
                            $displayLabel = $newHist->display?->treetext ?: 'display';
                            $user->notify(new WorkspaceNotification([
                                'category' => 'Task Update',
                                'title' => $resultFailed ? 'Task completed with issues' : 'Task completed successfully',
                                'body' => trim(($newHist->name ?: 'Task') . ' finished for ' . $displayLabel . '.'),
                                'severity' => $resultFailed ? 'danger' : 'success',
                                'icon' => $resultFailed ? 'clipboard-x' : 'clipboard-check',
                                'url' => url('histories/' . $newHist->id),
                                'scope' => $this->facility?->name,
                            ]));
                        });
                } catch (\Exception $e) {
                    logger()->error('TaskCompletedNotification failed: ' . $e->getMessage());
                }
            }
        }

        // check if we need to send alert for display status changed
        foreach ($results as $displayId => $history) {
            $display = Display::find($displayId);
            $oldStatus = $display->status;
            $display->errors = [$history->id];
            if ($history['result'] == 3) {
                $display->status = Display::STATUS_FAILED;
            } else if ($history['result'] == 2) {
                $display->status = Display::STATUS_OK;
            }
            if ($oldStatus != $display->status) {
                $display->errors = json_encode($display->errors);
                $display->save();
                // send alert
                $alerts = Alert::where([['facility_id', $this->facility->id], ['actived', 1]])->with('user')->get();
                foreach ($alerts as $alert) {
                    if (!config('app.offline')) {
                        $alert->notify(new DisplayStatusChangedNotification($display, $oldStatus, $display->status, $history));
                    }
                }

                $alerts->pluck('user')
                    ->filter()
                    ->unique('id')
                    ->each(function ($user) use ($display) {
                        $failed = (int) $display->status === Display::STATUS_FAILED;
                        $user->notify(new WorkspaceNotification([
                            'category' => 'Display Health',
                            'title' => $failed ? 'Display needs attention' : 'Display recovered',
                            'body' => trim(($display->treetext ?: 'Display') . ($failed ? ' reported a failed status.' : ' returned to an OK state.')),
                            'severity' => $failed ? 'danger' : 'success',
                            'icon' => $failed ? 'monitor-warning' : 'monitor-check',
                            'url' => url('display-settings/' . $display->id),
                            'scope' => $this->facility?->name,
                        ]));
                    });
            }
        }
    }
}
