<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'path', 'position'];

    public function product() { return $this->belongsTo(Product::class); }

    public function getUrlAttribute(): string
    {
        return filter_var($this->path, FILTER_VALIDATE_URL) ? $this->path : asset('storage/' . $this->path);
    }
}
