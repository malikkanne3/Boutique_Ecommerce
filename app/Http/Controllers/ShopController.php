<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;

class ShopController extends Controller
{
    public function index()
    {
        $query = Product::active()->inStock()->with('category');

        if (request('category')) {
            $query->byCategory(request('category'));
        }
        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        switch (request('sort', 'latest')) {
            case 'price_asc':  $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'name':       $query->orderBy('name', 'asc'); break;
            default:           $query->latest(); break;
        }

        $products   = $query->paginate(12);
        $categories = Category::withCount('products')->get();
        $allCount   = Product::active()->inStock()->count();

        return view('shop.index', compact('products', 'categories', 'allCount'));
    }

    public function show(Product $product)
    {
        $related = Product::active()->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('category')
            ->take(4)->get();

        return view('shop.show', compact('product', 'related'));
    }
}