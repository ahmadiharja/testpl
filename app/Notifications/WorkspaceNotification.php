<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkspaceNotification extends Notification
{
    use Queueable;

    public function __construct(protected array $payload)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return array_merge([
            'category' => 'Notification',
            'title' => 'New notification',
            'body' => '',
            'severity' => 'info',
            'icon' => 'bell',
            'url' => null,
            'scope' => null,
            'meta' => [],
        ], $this->payload);
    }
}
