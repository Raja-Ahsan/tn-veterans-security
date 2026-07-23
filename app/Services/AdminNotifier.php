<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AdminAlert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AdminNotifier
{
    public static function broadcast(
        string $title,
        string $body,
        string $icon = 'bell',
        ?string $url = null,
        string $category = 'general',
    ): void {
        try {
            $admins = User::query()->whereNotNull('email')->get();

            if ($admins->isEmpty()) {
                return;
            }

            Notification::send($admins, new AdminAlert($title, $body, $icon, $url, $category));
        } catch (\Throwable $exception) {
            Log::warning('Admin in-app notification failed', [
                'title' => $title,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
