<?php
/**
 * fix_all.php — Script complet d'amélioration du projet E-ShopSN
 * À placer et exécuter à la racine du projet Laravel
 */

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Démarrage du script fix_all.php ===\n\n";

// ─────────────────────────────────────────────
// 1. MISE À JOUR DES MIGRATIONS
// ─────────────────────────────────────────────
echo "1. Vérification des migrations...\n";

// Ajouter slug à products si absent
if (!Schema::hasColumn('products', 'slug')) {
    Schema::table('products', function ($t) {
        $t->string('slug')->nullable()->after('name');
    });
    echo "   ✅ Colonne slug ajoutée à products\n";
}

// Ajouter products_count virtuel (pas nécessaire, géré via withCount)

// ─────────────────────────────────────────────
// 2. MISE À JOUR DES MODÈLES
// ─────────────────────────────────────────────
echo "2. Mise à jour des modèles...\n";

// User: ajouter isAdmin() et role
file_put_contents('app/Models/User.php', <<<'PHP'
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden   = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
PHP);
echo "   ✅ User.php mis à jour\n";

// Category: ajouter products_count withCount
file_put_contents('app/Models/Category.php', <<<'PHP'
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'image'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
PHP);
echo "   ✅ Category.php mis à jour\n";

// Product: ajouter slug auto
file_put_contents('app/Models/Product.php', <<<'PHP'
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'price', 'stock', 'image', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price'     => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . uniqid();
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function scopeActive($query)    { return $query->where('is_active', true); }
    public function scopeInStock($query)   { return $query->where('stock', '>', 0); }
    public function scopeByCategory($q, $slug) {
        return $q->whereHas('category', fn($c) => $c->where('slug', $slug));
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }
        return $this->image
            ? asset('storage/' . $this->image)
            : 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&q=80';
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', ' ') . ' FCFA';
    }
}
PHP);
echo "   ✅ Product.php mis à jour\n";

// ─────────────────────────────────────────────
// 3. MISE À JOUR DES CONTRÔLEURS
// ─────────────────────────────────────────────
echo "3. Mise à jour des contrôleurs...\n";

// HomeController
file_put_contents('app/Http/Controllers/HomeController.php', <<<'PHP'
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
PHP);
echo "   ✅ HomeController mis à jour\n";

// ShopController
file_put_contents('app/Http/Controllers/ShopController.php', <<<'PHP'
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
PHP);
echo "   ✅ ShopController mis à jour\n";

// CheckoutController (fix total_amount)
file_put_contents('app/Http/Controllers/CheckoutController.php', <<<'PHP'
<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) return redirect()->route('cart.index');
        return view('checkout.index', compact('cart'));
    }

    public function store()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) return redirect()->route('cart.index');

        $this->validate(request(), [
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'required|email',
            'customer_phone'   => 'nullable|string|max:20',
            'shipping_address' => 'required|string',
        ]);

        DB::transaction(function () use ($cart) {
            $total = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
            $order = Order::create([
                'user_id'          => auth()->id(),
                'order_number'     => 'CMD-' . strtoupper(uniqid()),
                'total_amount'     => $total,
                'status'           => 'pending',
                'customer_name'    => request('customer_name'),
                'customer_email'   => request('customer_email'),
                'customer_phone'   => request('customer_phone'),
                'shipping_address' => request('shipping_address'),
            ]);

            foreach ($cart as $id => $item) {
                $order->products()->attach($id, [
                    'quantity' => $item['quantity'],
                    'price'    => $item['price'],
                ]);
                Product::find($id)?->decrement('stock', $item['quantity']);
            }

            session()->forget('cart');
        });

        return redirect()->route('orders.index')
            ->with('success', '🎉 Commande passée avec succès ! Nous vous contacterons rapidement.');
    }
}
PHP);
echo "   ✅ CheckoutController mis à jour\n";

// OrderController (pagination)
file_put_contents('app/Http/Controllers/OrderController.php', <<<'PHP'
<?php
namespace App\Http\Controllers;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('products')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        $order->load('products.category');
        return view('orders.show', compact('order'));
    }
}
PHP);
echo "   ✅ OrderController mis à jour\n";

// Admin controllers
file_put_contents('app/Http/Controllers/Admin/AdminProductController.php', <<<'PHP'
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
PHP);
echo "   ✅ AdminProductController mis à jour\n";

file_put_contents('app/Http/Controllers/Admin/AdminCategoryController.php', <<<'PHP'
<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store()
    {
        $data = request()->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $data['slug'] = Str::slug($data['name']);
        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success', 'Catégorie créée !');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Category $category)
    {
        $data = request()->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $data['slug'] = Str::slug($data['name']);
        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success', 'Catégorie mise à jour !');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Catégorie supprimée.');
    }

    public function show(Category $category)
    {
        return redirect()->route('admin.categories.edit', $category);
    }
}
PHP);
echo "   ✅ AdminCategoryController mis à jour\n";

file_put_contents('app/Http/Controllers/Admin/AdminOrderController.php', <<<'PHP'
<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Order;

class AdminOrderController extends Controller
{
    public function index()
    {
        $query = Order::with('user')->latest();
        if (request('search')) {
            $query->where(function($q) {
                $q->where('order_number', 'like', '%'.request('search').'%')
                  ->orWhere('customer_name', 'like', '%'.request('search').'%');
            });
        }
        if (request('status')) {
            $query->where('status', request('status'));
        }
        $orders   = $query->paginate(20);
        $statuses = Order::STATUSES;
        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order)
    {
        $order->load('products.category');
        $statuses = Order::STATUSES;
        return view('admin.orders.show', compact('order', 'statuses'));
    }

    public function update(Order $order)
    {
        $order->update(['status' => request('status')]);
        return redirect()->back()->with('success', 'Statut mis à jour !');
    }
}
PHP);
echo "   ✅ AdminOrderController mis à jour\n";

file_put_contents('app/Http/Controllers/Admin/AdminUserController.php', <<<'PHP'
<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        $query = User::withCount('orders')->latest();
        if (request('search')) {
            $query->where(function($q) {
                $q->where('name', 'like', '%'.request('search').'%')
                  ->orWhere('email', 'like', '%'.request('search').'%');
            });
        }
        $users = $query->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('orders');
        return view('admin.users.show', compact('user'));
    }
}
PHP);
echo "   ✅ AdminUserController mis à jour\n";

// Admin middleware
@mkdir('app/Http/Middleware', recursive: true);
file_put_contents('app/Http/Middleware/AdminMiddleware.php', <<<'PHP'
<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Accès refusé.');
        }
        return $next($request);
    }
}
PHP);
echo "   ✅ AdminMiddleware mis à jour\n";

// ─────────────────────────────────────────────
// 4. DONNÉES — CATÉGORIES ET PRODUITS
// ─────────────────────────────────────────────
echo "4. Insertion des données...\n";

// Vider les tables
DB::table('order_product')->delete();
DB::table('orders')->delete();
DB::table('products')->delete();
DB::table('categories')->delete();

// Catégories
$cats = [
    ['name' => 'Électronique',  'slug' => 'electronique',  'description' => 'Smartphones, ordinateurs, accessoires tech'],
    ['name' => 'Mode & Vêtements','slug' => 'mode',        'description' => 'Vêtements tendance hommes et femmes'],
    ['name' => 'Alimentation',  'slug' => 'alimentation',  'description' => 'Produits alimentaires, boissons, épicerie'],
    ['name' => 'Maison & Déco', 'slug' => 'maison',        'description' => 'Mobilier, décoration, articles ménagers'],
    ['name' => 'Beauté & Santé','slug' => 'beaute',        'description' => 'Cosmétiques, soins, parfums'],
    ['name' => 'Sport & Loisirs','slug' => 'sport',        'description' => 'Équipements sportifs, fitness'],
];

$catIds = [];
foreach ($cats as $c) {
    $cat = \App\Models\Category::create($c);
    $catIds[$c['slug']] = $cat->id;
}
echo "   ✅ " . count($cats) . " catégories créées\n";

// Produits avec photos Unsplash réelles
$products = [
    // ÉLECTRONIQUE
    ['name' => 'iPhone 15 Pro 128Go',        'slug' => 'iphone-15-pro', 'cat' => 'electronique', 'price' => 650000, 'stock' => 15, 'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=600&q=80', 'desc' => 'Le dernier iPhone d\'Apple avec puce A17 Pro, appareil photo 48MP et design en titane.'],
    ['name' => 'Samsung Galaxy S24',          'slug' => 'samsung-s24',   'cat' => 'electronique', 'price' => 480000, 'stock' => 20, 'image' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=600&q=80', 'desc' => 'Smartphone Android haut de gamme avec IA intégrée et écran Dynamic AMOLED 6,2 pouces.'],
    ['name' => 'MacBook Air M3',              'slug' => 'macbook-air-m3','cat' => 'electronique', 'price' => 950000, 'stock' => 8,  'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&q=80', 'desc' => 'Ordinateur portable ultra-fin avec la puce Apple M3, jusqu\'à 18h d\'autonomie.'],
    ['name' => 'AirPods Pro 2ème génération', 'slug' => 'airpods-pro-2', 'cat' => 'electronique', 'price' => 145000, 'stock' => 30, 'image' => 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=600&q=80', 'desc' => 'Écouteurs sans fil avec réduction de bruit active avancée et audio spatial.'],
    ['name' => 'iPad Pro 11 pouces',          'slug' => 'ipad-pro-11',   'cat' => 'electronique', 'price' => 580000, 'stock' => 12, 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=600&q=80', 'desc' => 'Tablette professionnelle avec écran Liquid Retina, puce M4 et compatibilité Apple Pencil.'],
    ['name' => 'Casque Sony WH-1000XM5',      'slug' => 'sony-wh1000xm5','cat' => 'electronique', 'price' => 175000, 'stock' => 25, 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=80', 'desc' => 'Casque à réduction de bruit leader du marché, 30h d\'autonomie et son Hi-Res.'],
    ['name' => 'Xiaomi Redmi Note 13',        'slug' => 'redmi-note-13', 'cat' => 'electronique', 'price' => 120000, 'stock' => 40, 'image' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=600&q=80', 'desc' => 'Smartphone polyvalent avec écran AMOLED 120Hz, batterie 5000mAh et charge rapide 67W.'],
    ['name' => 'Smart TV Samsung 55" 4K',     'slug' => 'samsung-tv-55', 'cat' => 'electronique', 'price' => 320000, 'stock' => 10, 'image' => 'https://images.unsplash.com/photo-1593784991095-a205069470b6?w=600&q=80', 'desc' => 'Télévision QLED 4K avec Tizen OS, HDR10+ et son Dolby Atmos pour une expérience cinéma.'],

    // MODE
    ['name' => 'T-Shirt Premium Blanc',       'slug' => 'tshirt-blanc',  'cat' => 'mode', 'price' => 8500,  'stock' => 100,'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=600&q=80', 'desc' => 'T-shirt en coton bio 100% de qualité supérieure. Coupe confortable, disponible en plusieurs tailles.'],
    ['name' => 'Jean Slim Fit Bleu',          'slug' => 'jean-slim-bleu','cat' => 'mode', 'price' => 25000, 'stock' => 60, 'image' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=600&q=80', 'desc' => 'Jean slim en denim stretch de haute qualité. Coupe moderne et confortable pour toutes les occasions.'],
    ['name' => 'Robe Wax Sénégalaise',        'slug' => 'robe-wax',      'cat' => 'mode', 'price' => 35000, 'stock' => 45, 'image' => 'https://images.unsplash.com/photo-1594938298603-c8148c4b5d28?w=600&q=80', 'desc' => 'Magnifique robe en tissu wax africain, confectionnée par des artisanes sénégalaises.'],
    ['name' => 'Sneakers Nike Air Max',       'slug' => 'nike-air-max',  'cat' => 'mode', 'price' => 75000, 'stock' => 35, 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80', 'desc' => 'Chaussures de sport iconiques Nike Air Max avec amorti révolutionnaire et style contemporain.'],
    ['name' => 'Sac à Main Cuir',             'slug' => 'sac-cuir',      'cat' => 'mode', 'price' => 45000, 'stock' => 28, 'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600&q=80', 'desc' => 'Sac à main en cuir véritable, fabriqué à la main. Élégant et spacieux pour toutes vos sorties.'],
    ['name' => 'Boubou Grand Modèle',         'slug' => 'boubou-grand',  'cat' => 'mode', 'price' => 55000, 'stock' => 20, 'image' => 'https://images.unsplash.com/photo-1605460375648-278bcbd579a6?w=600&q=80', 'desc' => 'Boubou traditionnel sénégalais en tissu de qualité, brodé à la main. Parfait pour les cérémonies.'],

    // ALIMENTATION
    ['name' => 'Café Arabica Premium 500g',   'slug' => 'cafe-arabica',  'cat' => 'alimentation', 'price' => 8000,  'stock' => 200,'image' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=600&q=80', 'desc' => 'Café arabica 100% de haute altitude, torréfié artisanalement. Arômes intenses et saveur équilibrée.'],
    ['name' => 'Huile d\'Argan Bio 100ml',    'slug' => 'huile-argan',   'cat' => 'alimentation', 'price' => 12000, 'stock' => 80, 'image' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=600&q=80', 'desc' => 'Huile d\'argan vierge et biologique, pressée à froid. Idéale pour la cuisine et les soins naturels.'],
    ['name' => 'Miel de Casamance 500g',      'slug' => 'miel-casamance','cat' => 'alimentation', 'price' => 6500,  'stock' => 150,'image' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=600&q=80', 'desc' => 'Miel naturel et pur récolté dans les forêts de Casamance. Sans additifs, riche en nutriments.'],
    ['name' => 'Thiéboudienne Mix Épices',    'slug' => 'thiebou-epices','cat' => 'alimentation', 'price' => 3500,  'stock' => 300,'image' => 'https://images.unsplash.com/photo-1532336414038-cf19250c5757?w=600&q=80', 'desc' => 'Mélange d\'épices authentiques pour préparer le fameux thiéboudienne, le plat national sénégalais.'],
    ['name' => 'Bissap Séché Premium 200g',   'slug' => 'bissap-seche',  'cat' => 'alimentation', 'price' => 2500,  'stock' => 500,'image' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=600&q=80', 'desc' => 'Fleurs de bissap (hibiscus) séchées de qualité supérieure. Parfaites pour préparer le jus traditionnel.'],

    // MAISON
    ['name' => 'Ventilateur sur Pied 16"',    'slug' => 'ventilateur-16','cat' => 'maison', 'price' => 28000, 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80', 'desc' => 'Ventilateur sur pied 16 pouces, 3 vitesses, oscillation automatique. Idéal pour combattre la chaleur.'],
    ['name' => 'Coussin Décoratif Set x4',    'slug' => 'coussins-deco', 'cat' => 'maison', 'price' => 15000, 'stock' => 80, 'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=600&q=80', 'desc' => 'Set de 4 coussins décoratifs aux motifs africains. Housse en tissu lavable, rembourrage ferme.'],
    ['name' => 'Lampe LED Solaire',           'slug' => 'lampe-solaire', 'cat' => 'maison', 'price' => 12000, 'stock' => 120,'image' => 'https://images.unsplash.com/photo-1524484485831-a92ffc0de03f?w=600&q=80', 'desc' => 'Lampe LED solaire rechargeable, parfaite pour les zones sans électricité ou les économies d\'énergie.'],
    ['name' => 'Service à Thé en Céramique',  'slug' => 'service-the',   'cat' => 'maison', 'price' => 22000, 'stock' => 35, 'image' => 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=600&q=80', 'desc' => 'Beau service à thé en céramique artisanale, comprenant théière, 6 tasses et plateau assorti.'],

    // BEAUTÉ
    ['name' => 'Crème Karité Naturelle',      'slug' => 'creme-karite',  'cat' => 'beaute', 'price' => 5000,  'stock' => 200,'image' => 'https://images.unsplash.com/photo-1556228578-626b67df4db4?w=600&q=80', 'desc' => 'Crème au beurre de karité 100% naturel et bio. Hydratante et nourrissante pour peau et cheveux.'],
    ['name' => 'Parfum Oud Royal 50ml',       'slug' => 'parfum-oud',    'cat' => 'beaute', 'price' => 35000, 'stock' => 40, 'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683702?w=600&q=80', 'desc' => 'Parfum oriental de luxe à base d\'oud véritable. Sillage puissant et longue tenue, 12h de diffusion.'],
    ['name' => 'Kit Soin Visage Naturel',     'slug' => 'kit-soin',      'cat' => 'beaute', 'price' => 18000, 'stock' => 60, 'image' => 'https://images.unsplash.com/photo-1556228578-626b67df4db4?w=600&q=80', 'desc' => 'Kit complet de soins naturels : nettoyant, tonique et crème hydratante à base de plantes africaines.'],
    ['name' => 'Huile de Coco Vierge 250ml',  'slug' => 'huile-coco',    'cat' => 'beaute', 'price' => 4500,  'stock' => 180,'image' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=600&q=80', 'desc' => 'Huile de coco vierge pressée à froid, multiusage pour cheveux, peau et cuisine.'],

    // SPORT
    ['name' => 'Ballon de Football Taille 5', 'slug' => 'ballon-foot-5', 'cat' => 'sport', 'price' => 12000, 'stock' => 80, 'image' => 'https://images.unsplash.com/photo-1614632537239-e4a21b8e47e0?w=600&q=80', 'desc' => 'Ballon de football officiel taille 5, homologué FIFA. Cuir synthétique haute résistance.'],
    ['name' => 'Tapis de Yoga Antidérapant',  'slug' => 'tapis-yoga',    'cat' => 'sport', 'price' => 18000, 'stock' => 60, 'image' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=600&q=80', 'desc' => 'Tapis de yoga en TPE écologique, épaisseur 6mm, antidérapant. Idéal pour le yoga et le fitness.'],
    ['name' => 'Haltères 10kg la paire',      'slug' => 'halteres-10kg', 'cat' => 'sport', 'price' => 22000, 'stock' => 45, 'image' => 'https://images.unsplash.com/photo-1526506118085-60ce8714f8c5?w=600&q=80', 'desc' => 'Paire d\'haltères en fonte de 10kg avec revêtement caoutchouc antidérapant pour votre home gym.'],
    ['name' => 'Corde à Sauter Pro',          'slug' => 'corde-sauter',  'cat' => 'sport', 'price' => 6500,  'stock' => 120,'image' => 'https://images.unsplash.com/photo-1434682881908-b43d0467b798?w=600&q=80', 'desc' => 'Corde à sauter professionnelle avec câble acier, poignées ergonomiques et roulements à billes.'],
];

$created = 0;
foreach ($products as $p) {
    \App\Models\Product::create([
        'category_id' => $catIds[$p['cat']],
        'name'        => $p['name'],
        'slug'        => $p['slug'] . '-' . uniqid(),
        'description' => $p['desc'],
        'price'       => $p['price'],
        'stock'       => $p['stock'],
        'image'       => $p['image'],
        'is_active'   => true,
    ]);
    $created++;
}
echo "   ✅ $created produits créés avec photos réelles\n";

// ─────────────────────────────────────────────
// 5. CRÉER UN COMPTE ADMIN
// ─────────────────────────────────────────────
echo "5. Création du compte admin...\n";

$admin = \App\Models\User::updateOrCreate(
    ['email' => 'admin@eshopsn.com'],
    [
        'name'     => 'Admin E-ShopSN',
        'password' => bcrypt('admin123'),
        'role'     => 'admin',
    ]
);
echo "   ✅ Admin créé : admin@eshopsn.com / admin123\n";

// ─────────────────────────────────────────────
// 6. AJOUTER COLONNE ROLE À USERS
// ─────────────────────────────────────────────
if (!Schema::hasColumn('users', 'role')) {
    Schema::table('users', function ($t) {
        $t->string('role')->default('user')->after('email');
    });
    echo "6. ✅ Colonne role ajoutée à users\n";
}

echo "\n=== ✅ SCRIPT TERMINÉ AVEC SUCCÈS ! ===\n";
echo "Accédez à : http://127.0.0.1:8000\n";
echo "Admin      : http://127.0.0.1:8000/admin\n";
echo "Login admin: admin@eshopsn.com / admin123\n";
