<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'size', 'color', 'stock'];

    public function product() { return $this->belongsTo(Product::class); }

    public function getLabelAttribute(): string
    {
        return trim(($this->size ?: '') . ($this->size && $this->color ? ' · ' : '') . ($this->color ?: '')) ?: 'Standard';
    }
}
