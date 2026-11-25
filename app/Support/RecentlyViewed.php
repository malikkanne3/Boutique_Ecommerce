<?php
namespace App\Support;

use App\Models\Product;

/** Produits récemment consultés (en session, valable même sans compte). */
class RecentlyViewed
{
    public static function push(int $productId): void
    {
        $ids = array_values(array_unique(array_merge([$productId], session('recent', []))));
        session()->put('recent', array_slice($ids, 0, 12));
    }

    public static function products(?int $except = null, int $limit = 8)
    {
        $ids = array_values(array_filter(session('recent', []), fn ($i) => $i !== $except));
        if (empty($ids)) {
            return collect();
        }

        $products = Product::active()->with(['category', 'seller', 'variants'])->whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)->map(fn ($id) => $products->get($id))->filter()->take($limit)->values();
    }
}
