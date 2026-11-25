<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Support\RecentlyViewed;

class ShopController extends Controller
{
    public function index()
    {
        $query = Product::active()->inStock()->with(['category', 'seller', 'variants']);

        if (request('category')) {
            $query->byCategory(request('category'));
        }
        if ($s = request('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', '%' . $s . '%')->orWhere('description', 'like', '%' . $s . '%');
            });
        }
        if (is_numeric(request('min'))) {
            $query->where('price', '>=', (float) request('min'));
        }
        if (is_numeric(request('max'))) {
            $query->where('price', '<=', (float) request('max'));
        }
        if (request('seller')) {
            $query->where('seller_id', (int) request('seller'));
        }

        switch (request('sort', 'latest')) {
            case 'price_asc':  $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'name':       $query->orderBy('name', 'asc'); break;
            default:           $query->latest(); break;
        }

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)->where('stock', '>', 0)])
            ->orderBy('name')->get();
        $allCount   = Product::active()->inStock()->count();
        $sellers    = User::where('role', 'vendeur')
            ->whereHas('products', fn ($q) => $q->where('is_active', true))
            ->orderBy('shop_name')->get();

        return view('shop.index', compact('products', 'categories', 'allCount', 'sellers'));
    }

    public function show(Product $product)
    {
        $user = auth()->user();
        $canSeeHidden = $user && ($user->isAdmin() || (int) $product->seller_id === (int) $user->id);
        abort_unless($product->is_active || $canSeeHidden, 404);

        $product->load(['category', 'seller', 'variants', 'images']);

        $related = Product::active()->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category', 'seller', 'variants'])
            ->take(4)->get();

        RecentlyViewed::push($product->id);
        $recent = RecentlyViewed::products($product->id, 6);

        $reviews = Review::with('user')->where('product_id', $product->id)->where('is_hidden', false)->latest()->take(10)->get();
        $distribution = Review::where('product_id', $product->id)->where('is_hidden', false)
            ->selectRaw('rating, COUNT(*) as c')->groupBy('rating')->pluck('c', 'rating');

        $onVacation = $product->seller && $product->seller->on_vacation;

        return view('shop.show', compact('product', 'related', 'recent', 'reviews', 'distribution', 'onVacation'));
    }
}
