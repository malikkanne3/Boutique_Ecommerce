<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::withTrashed()->with('category')->latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store()
    {
        $data = request()->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|max:2048',
            'image_url'   => 'nullable|url',
            'is_active'   => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . uniqid();
        $data['is_active'] = request()->has('is_active');

        if (request()->hasFile('image')) {
            $data['image'] = request()->file('image')->store('products', 'public');
        } elseif (request('image_url')) {
            $data['image'] = request('image_url');
        }

        Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Produit créé avec succès !');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Product $product)
    {
        $data = request()->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|max:2048',
            'image_url'   => 'nullable',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = request()->has('is_active');

        if (request()->hasFile('image')) {
            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = request()->file('image')->store('products', 'public');
        } elseif (request('image_url')) {
            $data['image'] = request('image_url');
        }

        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Produit mis à jour !');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produit supprimé.');
    }

    public function show(Product $product)
    {
        return redirect()->route('admin.products.edit', $product);
    }
}