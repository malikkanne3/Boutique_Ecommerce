<?php
namespace App\Http\Controllers;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
{
    $cart = session()->get('cart', []);
    $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    return view('cart.index', compact('cart', 'total'));
}

    public function add(Product $product)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image,
            ];
        }
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Produit ajouté au panier');
    }

    public function update(Product $product)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] = request('quantity');
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Panier mis à jour');
    }

    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);
        unset($cart[$product->id]);
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Produit supprimé');
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Panier vidé');
    }
}