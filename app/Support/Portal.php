<?php
namespace App\Support;

/** Chemins secrets des espaces admin / vendeur / livreur. */
class Portal
{
    public static function slug(string $role): string
    {
        return (string) config('portals.' . $role);
    }

    public static function path(string $role, string $suffix = ''): string
    {
        return '/' . self::slug($role) . ($suffix !== '' ? '/' . ltrim($suffix, '/') : '');
    }

    public static function url(string $role, string $suffix = ''): string
    {
        return url(self::path($role, $suffix));
    }
}
