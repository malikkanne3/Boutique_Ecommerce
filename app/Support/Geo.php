<?php
namespace App\Support;

use Illuminate\Support\Collection;

class Geo
{
    /** Distance à vol d'oiseau en km. */
    public static function km(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Tournée conseillée : on va toujours vers l'adresse la plus proche (plus proche voisin).
     * Les commandes sans position GPS sont placées à la fin.
     *
     * @return array<int, array{order: mixed, leg: ?float}>
     */
    public static function tour(Collection $orders, ?float $startLat = null, ?float $startLng = null): array
    {
        $withGps = $orders->filter(fn ($o) => $o->has_exact_location)->values();
        $without = $orders->reject(fn ($o) => $o->has_exact_location)->values();

        $route = [];
        $curLat = $startLat;
        $curLng = $startLng;
        $remaining = $withGps->all();

        // Sans point de départ, on part de la plus ancienne commande
        if ($curLat === null && ! empty($remaining)) {
            $first = array_shift($remaining);
            $route[] = ['order' => $first, 'leg' => null];
            $curLat = (float) $first->delivery_lat;
            $curLng = (float) $first->delivery_lng;
        }

        while (! empty($remaining)) {
            $bestKey = null;
            $bestDist = INF;
            foreach ($remaining as $k => $o) {
                $d = self::km($curLat, $curLng, (float) $o->delivery_lat, (float) $o->delivery_lng);
                if ($d < $bestDist) {
                    $bestDist = $d;
                    $bestKey = $k;
                }
            }
            $next = $remaining[$bestKey];
            unset($remaining[$bestKey]);
            $route[] = ['order' => $next, 'leg' => round($bestDist, 1)];
            $curLat = (float) $next->delivery_lat;
            $curLng = (float) $next->delivery_lng;
        }

        foreach ($without as $o) {
            $route[] = ['order' => $o, 'leg' => null];
        }

        return $route;
    }
}
