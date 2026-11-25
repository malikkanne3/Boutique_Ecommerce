<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;

/** Page publique d'une boutique (logo, bannière, description, horaires, produits, avis). */
class ShopPageController extends Controller
{
    public function show(User $seller)
    {
        abort_unless($seller->isVendeur() && $seller->account_status === 'active', 404);

        $products = Product::active()->inStock()->where('seller_id', $seller->id)
            ->with(['category', 'seller', 'variants'])->latest()->paginate(12);

        $reviews = Review::with('user')->whereNull('product_id')->where('seller_id', $seller->id)->where('is_hidden', false)->latest()->take(5)->get();
        $stats = [
            'products'  => Product::active()->where('seller_id', $seller->id)->count(),
            'delivered' => Order::where('seller_id', $seller->id)->where('status', 'delivered')->count(),
        ];

        $url = route('shops.show', $seller);
        $text = urlencode('Découvrez la boutique ' . $seller->display_name . ' sur E-Shop SN : ' . $url);
        $share = [
            'whatsapp' => 'https://wa.me/?text=' . $text,
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url),
            'x'        => 'https://twitter.com/intent/tweet?text=' . $text,
        ];

        return view('shops.show', compact('seller', 'products', 'reviews', 'stats', 'share', 'url'));
    }
}
