<?php
namespace App\Helpers;
use App\Models\Notification;

class NotifHelper
{
    public static function send(int $userId, string $title, string $body, string $icon = 'bell', string $color = 'primary', string $link = null): void
    {
        Notification::create([
            'user_id' => $userId,
            'title'   => $title,
            'body'    => $body,
            'icon'    => $icon,
            'color'   => $color,
            'link'    => $link,
        ]);
    }
}