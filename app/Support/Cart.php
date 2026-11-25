<?php
namespace App\Support;

use App\Models\Product;

/**
 * Panier en session. Clé d'une ligne : "{produit}:{variante}" (variante 0 = produit sans taille/couleur).
 * Prix, stock et disponibilité sont toujours recalculés depuis la base.
 */
class Cart
{
    public static function key(int $productId, int $variantId = 0): string
    {
        return $productId . ':' . $variantId;
    }

    /** @return array{0: \Illuminate\Support\Collection, 1: float|int} [lignes, total] */
    public static function items(): array
    {
        $cart = session('cart', []);

        $productIds = collect(array_keys($cart))
            ->map(fn ($k) => (int) explode(':', (string) $k)[0])->unique()->all();
        $products = Product::with(['seller', 'variants'])->whereIn('id', $productIds)->get()->keyBy('id');

        $items = collect();
        $clean = [];

        foreach ($cart as $key => $row) {
            $parts = array_pad(explode(':', (string) $key), 2, 0);
            $pid = (int) $parts[0];
            $vid = (int) $parts[1];

            $p = $products->get($pid);
            if (! $p || ! $p->is_active || ! $p->is_available || ($p->seller && $p->seller->on_vacation)) {
                continue;
            }

            $variant = null;
            if ($p->variants->isNotEmpty()) {
                $variant = $p->variants->firstWhere('id', $vid);
                if (! $variant) {
                    continue; // variante supprimée ou jamais choisie
                }
                $stock = (int) $variant->stock;
            } else {
                $stock = (int) $p->stock;
            }
            if ($stock < 1) {
                continue;
            }

            $qty  = min(max((int) ($row['quantity'] ?? 1), 1), $stock);
            $unit = $p->priceForQty($qty);
            $newKey = self::key($pid, $variant ? $variant->id : 0);

            $clean[$newKey] = ['quantity' => $qty];
            $items->push((object) [
                'key'        => $newKey,
                'product'    => $p,
                'variant'    => $variant,
                'quantity'   => $qty,
                'stock'      => $stock,
                'unit_price' => $unit,
                'subtotal'   => $unit * $qty,
                'saved'      => max(0, ((float) $p->price - $unit) * $qty),
            ]);
        }

        session()->put('cart', $clean);

        return [$items, $items->sum('subtotal')];
    }

    public static function count(): int
    {
        return (int) array_sum(array_column(session('cart', []), 'quantity'));
    }
}
