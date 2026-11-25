<?php
namespace App\Support;

use Illuminate\Support\Collection;

class Stats
{
    /** Série journalière [labels, values] à partir d'une collection de commandes. */
    public static function daily(Collection $orders, int $days = 14): array
    {
        $byDay = $orders->groupBy(fn ($o) => $o->created_at->format('Y-m-d'));
        $labels = [];
        $values = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $labels[] = $day->format('d/m');
            $values[] = (float) ($byDay->get($day->format('Y-m-d'), collect())->sum('total_amount'));
        }
        return ['labels' => $labels, 'values' => $values];
    }

    public static function money($amount): string
    {
        return number_format((float) $amount, 0, ',', ' ') . ' FCFA';
    }
}
