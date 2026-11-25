<?php
namespace App\Services;

use App\Helpers\BrevoMail;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use App\Support\Portal;

/** Notifications internes + e-mails, sans jamais faire planter la requête. */
class Notifier
{
    public static function notify(?int $userId, string $title, string $body, string $icon = 'bell', string $color = 'primary', ?string $link = null, ?int $actorId = null): void
    {
        if (! $userId) {
            return;
        }
        try {
            Notification::create([
                'user_id' => $userId,
                'title'   => $title,
                'body'    => $body,
                'icon'    => $icon,
                'color'   => $color,
                'link'    => $link,
                'actor_id' => $actorId,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function mail(?string $email, ?string $name, string $subject, string $html): void
    {
        if (! $email) {
            return;
        }
        try {
            $body = '<div style="font-family:Arial,sans-serif;max-width:560px;margin:auto;padding:20px;border:1px solid #eee;border-radius:12px">'
                . $html
                . '<hr style="border:none;border-top:1px solid #eee;margin:20px 0"><small style="color:#888">E-Shop SN</small></div>';
            BrevoMail::send($email, $name ?: $email, $subject, $body);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Notifie le vendeur de la commande, ou tous les admins pour la boutique officielle. */
    public static function toSellerSide(Order $order, string $title, string $body, string $icon, string $color, string $routePrefix = 'order', ?int $actorId = null): void
    {
        if ($order->seller_id) {
            self::notify($order->seller_id, $title, $body, $icon, $color, Portal::path('vendeur', 'orders/' . $order->id), $actorId);
            return;
        }
        foreach (User::where('role', 'admin')->pluck('id') as $adminId) {
            self::notify($adminId, $title, $body, $icon, $color, Portal::path('admin', 'orders/' . $order->id), $actorId);
        }
    }

    public static function sellerEmail(Order $order): array
    {
        if ($order->seller) {
            return [$order->seller->email, $order->seller->display_name];
        }
        return [env('ADMIN_EMAIL'), 'Admin'];
    }
}
