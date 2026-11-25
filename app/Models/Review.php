<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id', 'product_id', 'seller_id', 'order_id', 'rating', 'comment', 'reply', 'is_hidden'];

    protected $casts = ['is_hidden' => 'boolean'];

    protected static function booted(): void
    {
        static::saved(fn (Review $r) => $r->recalculate());
        static::deleted(fn (Review $r) => $r->recalculate());
    }

    public function user()    { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class)->withTrashed(); }
    public function seller()  { return $this->belongsTo(User::class, 'seller_id'); }

    public function scopeVisible($q) { return $q->where('is_hidden', false); }

    /** Recalcule la note moyenne du produit ou du vendeur concerné. */
    public function recalculate(): void
    {
        if ($this->product_id) {
            $q = Review::where('product_id', $this->product_id)->where('is_hidden', false);
            Product::withTrashed()->where('id', $this->product_id)
                ->update(['rating_avg' => round((float) $q->avg('rating'), 2), 'rating_count' => $q->count()]);
        } elseif ($this->seller_id) {
            $q = Review::whereNull('product_id')->where('seller_id', $this->seller_id)->where('is_hidden', false);
            User::where('id', $this->seller_id)
                ->update(['seller_rating_avg' => round((float) $q->avg('rating'), 2), 'seller_rating_count' => $q->count()]);
        }
    }
}
