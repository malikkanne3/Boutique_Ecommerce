<?php
namespace App\Support;

use App\Models\Favorite;

/** Favoris de l'utilisateur connecté (chargés une seule fois par requête). */
class Favorites
{
    private static ?array $ids = null;

    public static function ids(): array
    {
        if (self::$ids === null) {
            self::$ids = auth()->check() ? Favorite::where('user_id', auth()->id())->pluck('product_id')->all() : [];
        }
        return self::$ids;
    }

    public static function has(int $productId): bool
    {
        return in_array($productId, self::ids(), true);
    }
}
