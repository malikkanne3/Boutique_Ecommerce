<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Support\RecentlyViewed;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')->get();

        $featured    = Product::active()->inStock()->with(['category', 'seller', 'variants'])->latest()->take(8)->get();
        $newProducts = Product::active()->inStock()->with(['category', 'seller', 'variants'])->latest()->skip(8)->take(8)->get();

        $shops = User::where('role', 'vendeur')
            ->whereHas('products', fn ($q) => $q->where('is_active', true))
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->take(6)->get();

        $recent = RecentlyViewed::products(null, 6);

        return view('home', compact('categories', 'featured', 'newProducts', 'shops', 'recent'));
    }
}
