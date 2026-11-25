<?php
namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Gestion des produits.
 * - Le vendeur gère SES produits : infos, photos, variantes, STOCK, disponibilité, réductions.
 * - L'admin supervise : il peut modifier les informations, masquer/afficher, réaffecter ou supprimer,
 *   mais n'a AUCUN accès au stock ni à la disponibilité.
 */
class ProductController extends Controller
{
    private function isAdmin(): bool
    {
        return auth()->user()->isAdmin();
    }

    private function prefix(): string
    {
        return $this->isAdmin() ? 'admin' : 'vendeur';
    }

    private function scoped()
    {
        $q = Product::query();
        if (! $this->isAdmin()) {
            $q->where('seller_id', auth()->id());
        }
        return $q;
    }

    private function authorizeProduct(Product $product): void
    {
        abort_unless($this->isAdmin() || (int) $product->seller_id === (int) auth()->id(), 403);
    }

    private function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'category_id'        => 'required|exists:categories,id',
            'description'        => 'nullable|string|max:5000',
            'price'              => 'required|numeric|min:0',
            'stock'              => 'nullable|integer|min:0|max:100000',
            'images'             => 'nullable|array|max:6',
            'images.*'           => 'image|max:3072',
            'remove_images'      => 'nullable|array',
            'remove_images.*'    => 'integer',
            'image_url'          => 'nullable|url|max:500',
            'seller_id'          => 'nullable|exists:users,id',
            'is_active'          => 'nullable',
            'is_available'       => 'nullable',
            'discount_percent'   => 'nullable|integer|min:1|max:90',
            'discount_starts_at' => 'nullable|date',
            'discount_ends_at'   => 'nullable|date|after_or_equal:discount_starts_at',
            'bulk_min_qty'       => 'nullable|integer|min:2|max:10000',
            'bulk_percent'       => 'nullable|integer|min:1|max:90|required_with:bulk_min_qty',
            'variants'           => 'nullable|array|max:60',
            'variants.*.size'    => 'nullable|string|max:40',
            'variants.*.color'   => 'nullable|string|max:40',
            'variants.*.stock'   => 'nullable|integer|min:0|max:100000',
        ];
    }

    /** Photos : ajout, suppression, photo principale. */
    private function saveImages(Product $product, array $data): void
    {
        $removed = false;
        foreach ($data['remove_images'] ?? [] as $id) {
            $img = $product->images()->find($id);
            if ($img) {
                if (! filter_var($img->path, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($img->path);
                }
                $img->delete();
                $removed = true;
            }
        }

        $pos = (int) $product->images()->max('position');
        foreach (request()->file('images', []) ?? [] as $file) {
            $path = $file->store('products', 'public');
            $product->images()->create(['path' => $path, 'position' => ++$pos]);
        }

        $first = $product->images()->first();
        if ($first) {
            $product->update(['image' => $first->path]);
        } elseif (! empty($data['image_url'])) {
            $product->update(['image' => $data['image_url']]);
        } elseif ($removed) {
            $product->update(['image' => null]);
        }
    }

    /** Variantes (taille/couleur) : mise à jour par combinaison, le stock produit = somme des variantes. */
    private function saveVariants(Product $product, array $rows): void
    {
        $clean = collect($rows)->map(function ($r) {
            $size  = trim((string) ($r['size'] ?? ''));
            $color = trim((string) ($r['color'] ?? ''));
            return ['size' => $size !== '' ? $size : null, 'color' => $color !== '' ? $color : null, 'stock' => (int) ($r['stock'] ?? 0)];
        })->filter(fn ($r) => $r['size'] || $r['color']);

        $keyOf = fn ($size, $color) => mb_strtolower(($size ?? '') . '|' . ($color ?? ''));

        $existing = $product->variants()->get()->keyBy(fn ($v) => $keyOf($v->size, $v->color));
        $kept = [];

        foreach ($clean as $r) {
            $k = $keyOf($r['size'], $r['color']);
            if (isset($kept[$k])) {
                continue; // doublon
            }
            if ($existing->has($k)) {
                $existing[$k]->update(['stock' => $r['stock']]);
            } else {
                $product->variants()->create($r);
            }
            $kept[$k] = true;
        }
        foreach ($existing as $k => $variant) {
            if (! isset($kept[$k])) {
                $variant->delete();
            }
        }

        $product->unsetRelation('variants');
        $product->syncStockFromVariants();
    }

    public function index()
    {
        $query = $this->scoped()->with(['category', 'seller', 'variants'])->latest();

        if ($s = request('search')) {
            $query->where('name', 'like', '%' . $s . '%');
        }
        if ($c = request('category')) {
            $query->where('category_id', $c);
        }
        if (request('stock') === 'low') {
            $query->where('stock', '>', 0)->where('stock', '<=', Product::LOW_STOCK);
        } elseif (request('stock') === 'out') {
            $query->where(function ($q) {
                $q->where('stock', '<=', 0)->orWhere('is_available', false);
            });
        }
        if (request('state') === 'inactive') {
            $query->where('is_active', false);
        }

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        $summary = [
            'total'  => $this->scoped()->count(),
            'active' => $this->scoped()->where('is_active', true)->count(),
            'low'    => $this->scoped()->where('stock', '>', 0)->where('stock', '<=', Product::LOW_STOCK)->count(),
            'out'    => $this->scoped()->where(function ($q) {
                $q->where('stock', '<=', 0)->orWhere('is_available', false);
            })->count(),
        ];

        return view('manage.products.index', ['prefix' => $this->prefix()] + compact('products', 'categories', 'summary'));
    }

    public function create()
    {
        abort_if($this->isAdmin(), 403);

        return view('manage.products.form', [
            'prefix'     => $this->prefix(),
            'product'    => new Product(['is_active' => true, 'is_available' => true, 'stock' => 1]),
            'categories' => Category::orderBy('name')->get(),
            'sellers'    => collect(),
        ]);
    }

    public function store()
    {
        abort_if($this->isAdmin(), 403);
        $data = request()->validate($this->rules());

        $payload = Arr::only($data, ['name', 'category_id', 'description', 'price', 'bulk_min_qty', 'bulk_percent']);
        $payload['is_active']    = request()->boolean('is_active');
        $payload['is_available'] = request()->boolean('is_available');
        $payload['stock']        = (int) ($data['stock'] ?? 0);
        $payload['slug']         = Str::slug($data['name']) . '-' . uniqid();
        $payload['seller_id']    = auth()->id();
        $payload += $this->discountFields($data);

        $product = Product::create($payload);
        $this->saveImages($product, $data);
        $this->saveVariants($product, $data['variants'] ?? []);

        return redirect()->route('vendeur.products.index')->with('success', 'Produit créé avec succès !');
    }

    public function edit(Product $product)
    {
        $this->authorizeProduct($product);
        $product->load(['variants', 'images']);

        return view('manage.products.form', [
            'prefix'     => $this->prefix(),
            'product'    => $product,
            'categories' => Category::orderBy('name')->get(),
            'sellers'    => $this->isAdmin() ? User::where('role', 'vendeur')->orderBy('name')->get() : collect(),
        ]);
    }

    public function update(Product $product)
    {
        $this->authorizeProduct($product);
        $data = request()->validate($this->rules());

        $payload = Arr::only($data, ['name', 'category_id', 'description', 'price', 'bulk_min_qty', 'bulk_percent']);
        $payload['is_active'] = request()->boolean('is_active');
        $payload += $this->discountFields($data);

        if ($this->isAdmin()) {
            // Pas d'accès au stock, à la disponibilité ni aux variantes pour l'admin
            $payload['seller_id'] = request('seller_id') ?: null;
        } else {
            $payload['is_available'] = request()->boolean('is_available');
            $payload['stock']        = (int) ($data['stock'] ?? $product->stock);
        }

        $product->update($payload);
        $this->saveImages($product, $data);
        if (! $this->isAdmin()) {
            $this->saveVariants($product, $data['variants'] ?? []);
        }

        return redirect()->route($this->prefix() . '.products.index')->with('success', 'Produit mis à jour !');
    }

    private function discountFields(array $data): array
    {
        if (empty($data['discount_percent'])) {
            return ['discount_percent' => null, 'discount_starts_at' => null, 'discount_ends_at' => null];
        }
        return [
            'discount_percent'   => (int) $data['discount_percent'],
            'discount_starts_at' => $data['discount_starts_at'] ?? null,
            'discount_ends_at'   => $data['discount_ends_at'] ?? null,
        ];
    }

    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);
        $product->delete();
        return redirect()->route($this->prefix() . '.products.index')->with('success', 'Produit supprimé.');
    }

    /** Visible / masqué dans la boutique (modération possible par l'admin). */
    public function toggle(Product $product)
    {
        $this->authorizeProduct($product);
        $product->update(['is_active' => ! $product->is_active]);
        return back()->with('success', $product->is_active ? 'Produit visible dans la boutique.' : 'Produit masqué de la boutique.');
    }

    /** Stock d'un produit simple : réservé au vendeur. */
    public function stock(Product $product)
    {
        abort_if($this->isAdmin(), 403, 'Seul le vendeur gère le stock.');
        $this->authorizeProduct($product);

        if ($product->variants()->exists()) {
            return back()->with('error', 'Ce produit a des tailles/couleurs : modifiez le stock de chaque variante dans la fiche produit.');
        }
        $data = request()->validate(['stock' => 'required|integer|min:0|max:100000']);
        $product->update($data);
        return back()->with('success', 'Stock mis à jour.');
    }

    /** Disponible / en rupture (manuel) : réservé au vendeur. */
    public function availability(Product $product)
    {
        abort_if($this->isAdmin(), 403, 'Seul le vendeur gère la disponibilité.');
        $this->authorizeProduct($product);
        $product->update(['is_available' => ! $product->is_available]);
        return back()->with('success', $product->is_available ? 'Produit marqué « disponible ».' : 'Produit marqué « en rupture ».');
    }
}
