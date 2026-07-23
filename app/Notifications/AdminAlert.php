<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminAlert extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public string $icon = 'bell',
        public ?string $url = null,
        public string $category = 'general',
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array{title: string, body: string, icon: string, url: ?string, category: string}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'icon' => $this->icon,
            'url' => $this->url,
            'category' => $this->category,
        ];
    }
}
