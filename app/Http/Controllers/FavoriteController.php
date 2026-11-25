<?php
namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;

class FavoriteController extends Controller
{
    public function index()
    {
        $products = Product::active()->with(['category', 'seller', 'variants'])
            ->whereIn('id', Favorite::where('user_id', auth()->id())->pluck('product_id'))
            ->latest()->paginate(12);

        return view('favorites.index', compact('products'));
    }

    public function toggle(Product $product)
    {
        $existing = Favorite::where('user_id', auth()->id())->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Retiré de vos favoris.');
        }

        Favorite::create(['user_id' => auth()->id(), 'product_id' => $product->id]);
        return back()->with('success', 'Ajouté à vos favoris ❤️');
    }
}
