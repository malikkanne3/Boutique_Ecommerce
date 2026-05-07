<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'total_amount', 'status',
        'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'livreur_id',
    ];

    protected $casts = ['total_amount' => 'decimal:2'];

    const STATUSES = [
        'pending'    => 'En attente',
        'confirmed'  => 'Confirmée',
        'delivering' => 'En livraison',
        'delivered'  => 'Livrée',
        'cancelled'  => 'Annulée',
    ];

    public function user()      { return $this->belongsTo(User::class); }
    public function livreur()   { return $this->belongsTo(User::class, 'livreur_id'); }
    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'price')->withTimestamps();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'confirmed'  => 'primary',
            'delivering' => 'info',
            'delivered'  => 'success',
            'cancelled'  => 'danger',
            default      => 'warning',
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_amount, 2, ',', ' ') . ' FCFA';
    }
}