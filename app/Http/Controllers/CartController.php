<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;

class CartController extends Controller
{
    public function index()
    {
        [$items, $total] = Cart::items();
        $groups  = $items->groupBy(fn ($i) => $i->product->seller_id ?? 0);
        $savings = $items->sum('saved');

        return view('cart.index', compact('items', 'total', 'groups', 'savings'));
    }

    public function add(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load('variants');

        $product->loadMissing('seller');
        if ($product->seller && $product->seller->on_vacation) {
            return back()->with('error', 'Cette boutique est en vacances : vous ne pouvez pas commander pour le moment.');
        }
        if (! $product->is_available) {
            return back()->with('error', 'Ce produit est actuellement indisponible.');
        }

        $qty       = max(1, (int) request('quantity', 1));
        $variantId = 0;
        $stock     = (int) $product->stock;

        if ($product->variants->isNotEmpty()) {
            $variant = $product->variants->firstWhere('id', (int) request('variant'));
            if (! $variant) {
                return back()->with('error', 'Choisissez une taille et/ou une couleur avant d\'ajouter au panier.');
            }
            $variantId = $variant->id;
            $stock     = (int) $variant->stock;
        }

        if ($stock < 1) {
            return back()->with('error', 'Cette option est en rupture de stock.');
        }

        $key     = Cart::key($product->id, $variantId);
        $cart    = session()->get('cart', []);
        $current = (int) ($cart[$key]['quantity'] ?? 0);
        $wanted  = $current + $qty;
        $new     = min($wanted, $stock);

        $cart[$key] = ['quantity' => $new];
        session()->put('cart', $cart);

        if ($wanted > $new) {
            return back()->with('success', "Stock limité : {$new} exemplaire(s) dans votre panier.");
        }
        return back()->with('success', '« ' . $product->name . ' » ajouté au panier 🛒');
    }

    public function update(Product $product)
    {
        $key  = Cart::key($product->id, (int) request('variant', 0));
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            $qty = (int) request('quantity', 1);
            if ($qty < 1) {
                unset($cart[$key]);
            } else {
                $cart[$key]['quantity'] = $qty; // plafonné au stock par Cart::items()
            }
            session()->put('cart', $cart);
        }
        return back();
    }

    public function remove(Product $product)
    {
        $key  = Cart::key($product->id, (int) request('variant', 0));
        $cart = session()->get('cart', []);
        unset($cart[$key]);
        session()->put('cart', $cart);
        return back()->with('success', 'Produit retiré du panier');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Panier vidé');
    }
}
