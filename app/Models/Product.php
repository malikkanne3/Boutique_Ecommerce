<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    public const LOW_STOCK = 5;

    protected $fillable = [
        'category_id', 'seller_id', 'name', 'slug', 'description',
        'price', 'stock', 'image', 'is_active', 'is_available',
        'discount_percent', 'discount_starts_at', 'discount_ends_at',
        'bulk_min_qty', 'bulk_percent',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'is_available'       => 'boolean',
        'price'              => 'decimal:2',
        'discount_starts_at' => 'datetime',
        'discount_ends_at'   => 'datetime',
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

    public function category() { return $this->belongsTo(Category::class); }
    public function seller()   { return $this->belongsTo(User::class, 'seller_id'); }
    public function reviews()  { return $this->hasMany(Review::class)->where('is_hidden', false)->latest(); }
    public function variants() { return $this->hasMany(ProductVariant::class)->orderBy('id'); }
    public function images()   { return $this->hasMany(ProductImage::class)->orderBy('position')->orderBy('id'); }

    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('quantity', 'price', 'variant_id', 'size', 'color')
            ->withTimestamps();
    }

    /* ---------- Scopes ---------- */
    /** Produits visibles : publiés, et dont le vendeur n'est pas en vacances. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where(function ($q) {
            $q->whereNull('seller_id')->orWhereIn('seller_id', function ($s) {
                $s->select('id')->from('users')->where(function ($v) {
                    $v->where('vacation_mode', false)->orWhere(function ($w) {
                        $w->whereNotNull('vacation_until')->where('vacation_until', '<', today()->toDateString());
                    });
                });
            });
        });
    }
    public function scopeInStock($query) { return $query->where('is_available', true)->where('stock', '>', 0); }
    public function scopeByCategory($q, $slug)
    {
        return $q->whereHas('category', fn ($c) => $c->where('slug', $slug));
    }

    /* ---------- Disponibilité ---------- */
    public function getIsPurchasableAttribute(): bool
    {
        return $this->is_active && $this->is_available && $this->stock > 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->is_available && $this->stock > 0 && $this->stock <= self::LOW_STOCK;
    }

    public function getAvailabilityLabelAttribute(): string
    {
        if (! $this->is_available || $this->stock <= 0) {
            return 'Rupture de stock';
        }
        return $this->is_low_stock ? 'Stock faible' : 'Disponible';
    }

    /** Variantes (taille/couleur) : listes distinctes pour l'affichage. */
    public function getHasVariantsAttribute(): bool
    {
        return $this->variants->isNotEmpty();
    }

    public function getSizesAttribute()
    {
        return $this->variants->pluck('size')->filter()->unique()->values();
    }

    public function getColorsAttribute()
    {
        return $this->variants->pluck('color')->filter()->unique()->values();
    }

    /* ---------- Prix : réduction sur une période + tarif dégressif ---------- */
    public function getIsOnSaleAttribute(): bool
    {
        if (! $this->discount_percent) {
            return false;
        }
        if ($this->discount_starts_at && now()->lt($this->discount_starts_at)) {
            return false;
        }
        if ($this->discount_ends_at && now()->gt($this->discount_ends_at)) {
            return false;
        }
        return true;
    }

    /** Prix unitaire actuel (réduction appliquée), arrondi au FCFA. */
    public function getCurrentPriceAttribute(): float
    {
        $price = (float) $this->price;
        if ($this->is_on_sale) {
            $price = $price * (100 - (int) $this->discount_percent) / 100;
        }
        return (float) round($price);
    }

    /** Prix unitaire pour une quantité donnée (tarif dégressif). */
    public function priceForQty(int $qty): float
    {
        $price = $this->current_price;
        if ($this->bulk_min_qty && $this->bulk_percent && $qty >= $this->bulk_min_qty) {
            $price = $price * (100 - (int) $this->bulk_percent) / 100;
        }
        return (float) round($price);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->current_price, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedOriginalPriceAttribute(): string
    {
        return number_format((float) $this->price, 0, ',', ' ') . ' FCFA';
    }

    /* ---------- Images ---------- */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }
        return $this->image
            ? asset('storage/' . $this->image)
            : 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&q=80';
    }

    /** Toutes les photos pour la galerie (photo principale d'abord). */
    public function getGalleryAttribute(): array
    {
        $urls = [$this->image_url];
        foreach ($this->images as $img) {
            if (! in_array($img->url, $urls, true)) {
                $urls[] = $img->url;
            }
        }
        return $urls;
    }

    public function getSellerNameAttribute(): string
    {
        return $this->seller ? $this->seller->display_name : 'E-Shop SN';
    }

    /** Remet stock produit = somme des variantes (si le produit en a). */
    public function syncStockFromVariants(): void
    {
        if ($this->variants()->exists()) {
            $this->update(['stock' => (int) $this->variants()->sum('stock')]);
        }
    }
}
