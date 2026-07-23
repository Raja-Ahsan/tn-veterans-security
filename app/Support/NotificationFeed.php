<?php

namespace App\Support;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationFeed
{
    /**
     * @param  Collection<int, DatabaseNotification>  $notifications
     * @return array{unread_count: int, notifications: list<array{id: string, title: string, body: string, category: string, icon: string, created_at_human: string, read_url: string}>}
     */
    public static function payload(Collection $notifications, int $unreadCount, string $readRouteName): array
    {
        return [
            'unread_count' => $unreadCount,
            'notifications' => $notifications->map(function (DatabaseNotification $notification) use ($readRouteName): array {
                $data = $notification->data ?? [];

                return [
                    'id' => (string) $notification->id,
                    'title' => (string) ($data['title'] ?? 'Notification'),
                    'body' => (string) ($data['body'] ?? ''),
                    'category' => (string) ($data['category'] ?? 'general'),
                    'icon' => (string) ($data['icon'] ?? 'bell'),
                    'created_at_human' => $notification->created_at?->diffForHumans() ?? '',
                    'read_url' => route($readRouteName, $notification->id),
                ];
            })->values()->all(),
        ];
    }
}
