<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $categories  = Category::withCount('products')->get();
        $featured    = Product::active()->inStock()->with('category')->latest()->take(8)->get();
        $newProducts = Product::active()->inStock()->with('category')->latest()->skip(8)->take(8)->get();
        return view('home', compact('categories', 'featured', 'newProducts'));
    }
}