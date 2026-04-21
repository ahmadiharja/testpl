<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SchedulerWorkspaceNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function __construct(protected SchedulerWorkspaceNotificationService $schedulerWorkspaceNotificationService)
    {
    }

    public function page(Request $request)
    {
        return view('notifications.index', [
            'title' => 'Notifications',
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json([
                'data' => [],
                'unreadCount' => 0,
                'meta' => $this->emptyMeta(),
            ]);
        }

        $this->schedulerWorkspaceNotificationService->syncForUser($user);

        $filter = $request->get('filter') === 'all' ? 'all' : 'unread';
        $limit = max(1, min((int) $request->get('limit', 8), 20));
        $page = max(1, (int) $request->get('page', 1));

        $query = $this->queryForUser($user);
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        $total = (clone $query)->count();
        $lastPage = max(1, (int) ceil($total / $limit));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * $limit;

        $context = $request->get('context') === 'mobile' ? 'mobile' : 'desktop';

        $items = (clone $query)
            ->latest('created_at')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(fn (DatabaseNotification $notification) => $this->present($notification, $context))
            ->values();

        $from = $total > 0 ? ($offset + 1) : 0;
        $to = $total > 0 ? min($offset + $limit, $total) : 0;

        return response()->json([
            'data' => $items,
            'unreadCount' => $this->queryForUser($user)->whereNull('read_at')->count(),
            'meta' => [
                'currentPage' => $page,
                'lastPage' => $lastPage,
                'perPage' => $limit,
                'total' => $total,
                'from' => $from,
                'to' => $to,
            ],
        ]);
    }

    public function read(Request $request, string $id): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $notification = $this->queryForUser($user)->where('id', $id)->first();
        if (!$notification) {
            return response()->json(['success' => false], 404);
        }

        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'unreadCount' => $this->queryForUser($user)->whereNull('read_at')->count(),
        ]);
    }

    public function readAll(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $this->queryForUser($user)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'unreadCount' => 0,
        ]);
    }

    private function resolveUser(Request $request): ?User
    {
        return auth()->user() ?: User::find($request->session()->get('id'));
    }

    private function queryForUser(User $user)
    {
        return DatabaseNotification::query()
            ->where('notifiable_id', $user->id)
            ->whereIn('notifiable_type', [
                User::class,
                'App\User',
            ]);
    }

    private function present(DatabaseNotification $notification, string $context = 'desktop'): array
    {
        $data = is_array($notification->data)
            ? $notification->data
            : (json_decode($notification->data ?? '{}', true) ?: []);

        $payload = $this->normalizePayload($notification->type, $data, $context);

        return [
            'id' => $notification->id,
            'category' => $payload['category'],
            'title' => $payload['title'],
            'body' => $payload['body'],
            'severity' => $payload['severity'],
            'icon' => $payload['icon'],
            'url' => $payload['url'],
            'scope' => $payload['scope'],
            'read' => !is_null($notification->read_at),
            'relativeTime' => optional($notification->created_at)->diffForHumans() ?: 'Just now',
            'createdAt' => optional($notification->created_at)?->format('d M Y H:i') ?: '-',
        ];
    }

    private function normalizePayload(string $type, array $data, string $context = 'desktop'): array
    {
        if (!empty($data['title'])) {
            return [
                'category' => $data['category'] ?? 'Notification',
                'title' => $data['title'],
                'body' => $data['body'] ?? '',
                'severity' => $data['severity'] ?? 'info',
                'icon' => $data['icon'] ?? 'bell',
                'url' => $data['url'] ?? null,
                'scope' => $data['scope'] ?? null,
            ];
        }

        if (str_ends_with($type, 'MessageDBNotification')) {
            return $this->normalizeLegacyMessage($data['message_id'] ?? null, $context);
        }

        return [
            'category' => 'Notification',
            'title' => class_basename($type),
            'body' => 'A new notification was recorded for your workspace.',
            'severity' => 'info',
            'icon' => 'bell',
            'url' => null,
            'scope' => null,
        ];
    }

    private function normalizeLegacyMessage(?string $messageId, string $context = 'desktop'): array
    {
        $isMobile = $context === 'mobile';

        return match ($messageId) {
            'edit_profile_reminder' => [
                'category' => 'Account',
                'title' => 'Complete your profile',
                'body' => 'Review your profile details and confirm your remote access credentials.',
                'severity' => 'info',
                'icon' => 'user-round',
                'url' => $isMobile ? route('mobile.profile.settings') : url('profile-settings'),
                'scope' => null,
            ],
            'site_settings_reminder' => [
                'category' => 'Site Setup',
                'title' => 'Review site settings',
                'body' => 'Check branding, mail delivery, and release configuration for this workspace.',
                'severity' => 'info',
                'icon' => 'settings-2',
                'url' => $isMobile ? null : url('site-settings'),
                'scope' => null,
            ],
            'smtp_reminder' => [
                'category' => 'Mail Delivery',
                'title' => 'Review SMTP delivery',
                'body' => 'Confirm the outbound mail configuration used for alerts, reports, and test messages.',
                'severity' => 'warning',
                'icon' => 'mail-search',
                'url' => $isMobile ? null : url('site-settings?tab=smtp'),
                'scope' => null,
            ],
            default => [
                'category' => 'Notification',
                'title' => $messageId ? Str::headline($messageId) : 'New workspace notification',
                'body' => $messageId
                    ? 'Review the linked workspace flow for more details.'
                    : 'A new notification was added to your account.',
                'severity' => 'info',
                'icon' => 'bell',
                'url' => $isMobile ? route('mobile.notifications') : url('notifications'),
                'scope' => null,
            ],
        };
    }

    private function emptyMeta(): array
    {
        return [
            'currentPage' => 1,
            'lastPage' => 1,
            'perPage' => 0,
            'total' => 0,
            'from' => 0,
            'to' => 0,
        ];
    }
}
